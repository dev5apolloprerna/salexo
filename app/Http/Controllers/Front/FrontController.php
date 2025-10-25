<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\RequestForDemo;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Razorpay\Api\Api;

class FrontController extends Controller
{

    public function index(Request $request)
    {
        $monthly = Plan::where(['iStatus' => 1, 'isDelete' => 0, 'plan_id' => 2])->first();
        $sixmonthly = Plan::where(['iStatus' => 1, 'isDelete' => 0, 'plan_id' => 3])->first();
        $yearly = Plan::where(['iStatus' => 1, 'isDelete' => 0, 'plan_id' => 4])->first();

        return view('frontview.index', compact('monthly', 'sixmonthly', 'yearly'));
    }

    public function request_for_demo(Request $request)
    {
        try {
            $request->validate(
                [
                    'company_name' => 'required|string|max:255',
                    'contact_person_name' => 'required|string|max:255',
                    'mobile' => 'required|digits:10',
                    'situable_time' => 'required',
                ]
            );

            $data = array(
                'company_name' => $request->company_name,
                'contact_person_name' => $request->contact_person_name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'situable_time' => $request->situable_time,
                "created_at" => now(),
                "strIP" => $request->ip(),
            );
            RequestForDemo::create($data);

            $SendEmailDetails = DB::table('sendemaildetails')->where('id', 4)->first();

            if ($SendEmailDetails) {
                $msg = [
                    'FromMail' => $SendEmailDetails->strFromMail,
                    'Title' => $SendEmailDetails->strTitle,
                    'ToEmail' => $SendEmailDetails->ToMail,
                    'Subject' => $SendEmailDetails->strSubject
                ];

                // ✅ Send email
                Mail::send('emails.request_for_demo', ['data' => $data], function ($message) use ($msg) {
                    $message->from($msg['FromMail'], $msg['Title']);
                    $message->to($msg['ToEmail'])->subject($msg['Subject']);
                });
            }

            // ✅ Flash success message to session
            return redirect()->back()->with('demo_success', true);
        } catch (\Throwable $th) {
            Log::error('Contact Form Submission Error: ' . $th->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $th
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong while submitting the form. Please try again later.');
        }
    }

    public function registration(Request $request)
    {
        $states = State::orderBy('stateName', 'asc')->where(['istatus' => 1, 'isDelete' => 0])->get();
        // Get plan details from query parameters
        $plan = $request->input('plan', 'Starter');      // default to Starter
        $amount = $request->input('amount', 100);       // default ₹100
        $days = $request->input('days', 30);            // default 30 days

        return view('frontview.registration', compact('plan', 'amount', 'days', 'states'));
    }

    public function registration_store(Request $request)
    {
        // dd($request);
        try {
            $request->validate(
                [
                    'company_name' => 'required|string|max:255',
                    'gst' => 'nullable',
                    'contact_person_name' => 'required|string|max:255',
                    'mobile' => 'required|digits:10',
                    'email' => 'required|email',
                    'address' => 'required',
                    'pincode' => 'required',
                    'city' => 'required',
                    'state_id' => 'required',
                ]
            );

            $plan = Plan::where('plan_name', $request->plan_name)->first();
            // dd($plan);

            $data = array(
                'emp_id' => 0,
                'company_name' => $request->company_name,
                'contact_person_name' => $request->contact_person_name,
                'gst' => $request->gst,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'pincode' => $request->pincode,
                'city' => $request->city,
                'state_id' => $request->state_id ?? 0,

                'plan_name' => $plan->plan_id ?? 0,
                'duration_in_days' => $request->duration_in_days,
                'amount' => $request->amount ?? 0,
                'gst_percentage' => $request->gst_percentage ?? 0,
                'gst_amount' => $request->gst_amount ?? 0,
                'net_amount' => $request->net_amount ?? 0,
                'isPayment' => 0,

                "created_at" => now(),
                "strIP" => $request->ip(),
            );
            $order = Order::create($data);
            // dd($order);

            $api = new Api(config('app.razorpay_key'), config('app.razorpay_secret'));
            // dd($api);
            $amount = $request->net_amount;

            $razorpayOrder = $api->order->create([
                'receipt' => $order->id . '-' . date('YmdHis'),
                'amount' => $amount * 100,
                'currency' => 'INR',
            ]);

            Payment::create([
                'order_id' => $razorpayOrder['id'],
                'oid' => $order->id,
                'amount' => $amount,
                'currency' => 'INR',
                'receipt' => $razorpayOrder['receipt'],
            ]);
            // dd($razorpayOrder);

            DB::commit();

            return response()->json([
                'success' => true,
                'razorpay_order_id' => $razorpayOrder['id'],
                'amount' => $amount,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'customer_name' => $request->contact_person_name,
                'order_id' => $order->id
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Registration Store Error: ' . $th->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $th
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong while submitting the form. Please try again later.');
        }
    }


    public function login(Request $request)
    {
        // dd($request);

        if (Auth::guard('web_employees')->check()) {
            $user = Auth::guard('web_employees')->user();

            if ($user->role_id == 2) {
                return redirect()->route('userhome');
            } elseif ($user->role_id == 3) {
                return redirect()->route('employee.home');
            }
        }

        return view('frontview.login');
    }

    public function privacy_policy(Request $request)
    {
        return view('frontview.privacy_policy');
    }

    public function term_and_condition(Request $request)
    {
        return view('frontview.term_and_condition');
    }
}
