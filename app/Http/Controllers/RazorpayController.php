<?php

namespace App\Http\Controllers;

use App\Models\CompanyClient;
use Illuminate\Http\Request;
use App\Models\Payment;
use Razorpay\Api\Api;
use Redirect, Response;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

use App\Models\Employee;
use App\Models\LeadCancelReason;
use App\Models\LeadPipeline;
use App\Models\ProductAttributes;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class RazorpayController extends Controller
{

    public function razorPaySuccess(Request $request)
    {
        try {
            $orderId = $request->orderId;

            // Fetch payment and order
            $payment = Payment::where('oid', $orderId)->firstOrFail();
            $order = Order::where('id', $orderId)->firstOrFail();

            // Update Razorpay payment details
            $payment->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
                'razorpay_order_id' => $request->razorpay_order_id,
            ]);

            $stringdata = $request->razorpay_order_id . '|' . $request->razorpay_payment_id;
            $generated_signature = hash_hmac('sha256', $stringdata, config('app.razorpay_secret'));

            if ($generated_signature === $request->razorpay_signature) {

                Log::info('Payment verified for order: ' . $orderId);

                $payment->update([
                    'status' => 'Success',
                    'iPaymentType' => 1,
                    'Remarks' => 'Online Payment',
                ]);

                $order->update(['isPayment' => 1]);

                return response()->json(['id' => $orderId]);
            } else {
                $payment->update(['status' => 'Fail']);
                Log::warning('Signature mismatch on payment verification', ['order_id' => $orderId]);
                return response()->json(['id' => 0]);
            }
        } catch (\Throwable $e) {
            Log::error('RazorpayController@razorPaySuccess failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all(),
            ]);

            return response()->json(['id' => 0]);
        }
    }

    //after payment success function
    public function payment_success(Request $request, $id)
    {
        try {

            $order = Order::where("id", $id)->firstOrFail();
            $plainPassword = Str::random(8);

            // $employee = Employee::where('emp_mobile', $order->mobile)->first();
            $employee = Employee::where('emp_mobile', $order->mobile)
                ->orWhere('emp_email', $order->email)
                ->first();

            $isNew = false;
            $client = null;

            if (!$employee) {
                $isNew = true;

                $company_client = CompanyClient::where('mobile', $order->mobile)
                    ->orWhere('email', $order->email)
                    ->first();

                if (!$company_client) {
                    // Create new employee
                    $client = CompanyClient::create([
                        'company_name'            => $order->company_name,
                        'GST'                => $order->gst,
                        'contact_person_name'    => $order->contact_person_name,
                        'mobile'              => $order->mobile,
                        'email'              => $order->email,
                        'Address'             => $order->address,
                        'pincode'             => $order->pincode,
                        'city'             => $order->city,
                        'state_id'             => $order->state_id,
                        'password'                => Hash::make($plainPassword),
                        'subscription_start_date'  => now(),
                        'subscription_end_date'  => now()->addDays($order->duration_in_days ?? 0),
                        'plan_id'  => $order->plan_name,
                        'plan_amount'  => $order->amount,
                        'plan_days'  => $order->duration_in_days,
                    ]);
                } else {
                    $client = $company_client->company_id;
                }

                $employee = Employee::where('emp_mobile', $order->mobile)
                    ->orWhere('emp_email', $order->email)
                    ->first();

                if (!$employee) {
                    $employee = Employee::create([
                        'company_id'              => $client->company_id,
                        'emp_name'                => $order->contact_person_name,
                        'emp_mobile'              => $order->mobile,
                        'emp_email'               => $order->email,
                        'emp_loginId'             => $order->email,
                        'password'                => Hash::make($plainPassword),
                        'isCompanyAdmin'          => 1,
                        'can_access_LMS'          => 1,
                        'role_id'                 => 2
                    ]);
                } else {
                    $employee = $employee->emp_id;
                }

                $lead_pipelines = [
                    [
                        'company_id' => $client->company_id,
                        'pipeline_name' => 'New Lead',
                        'slugname' => Str::slug('New Lead'),
                        'admin' => 1,
                        'followup_needed' => 'no',
                        'color' => '#FF5733',
                        'icon' => '<i class="fa-solid fa-plus"></i>',
                        'created_at' => now()
                    ],
                    [
                        'company_id' => $client->company_id,
                        'pipeline_name' => 'Deal Done',
                        'slugname' => Str::slug('Deal Done'),
                        'admin' => 1,
                        'followup_needed' => 'no',
                        'color' => '#33C1FF',
                        'icon' => '<i class="fa-solid fa-check"></i>',
                        'created_at' => now()
                    ],
                    [
                        'company_id' => $client->company_id,
                        'pipeline_name' => 'Deal Pending',
                        'slugname' => Str::slug('Deal Pending'),
                        'admin' => 1,
                        'followup_needed' => 'no',
                        'color' => '#28A745',
                        'icon' => '<i class="fa-solid fa-hourglass-start"></i>',
                        'created_at' => now()
                    ],
                    [
                        'company_id' => $client->company_id,
                        'pipeline_name' => 'Deal Cancel',
                        'slugname' => Str::slug('Deal Cancel'),
                        'admin' => 1,
                        'followup_needed' => 'no',
                        'color' => '#FFC107',
                        'icon' => '<i class="fa-solid fa-xmark"></i>',
                        'created_at' => now()
                    ]
                ];

                foreach ($lead_pipelines as $lead_pipeline) {
                    LeadPipeline::create($lead_pipeline);
                }

                $lead_cancel_reasons = [
                    [
                        'company_id' => $client->company_id,
                        'reason' => 'No longer interested'
                    ],
                    [
                        'company_id' => $client->company_id,
                        'reason' => 'Budget constraints'
                    ],
                    [
                        'company_id' => $client->company_id,
                        'reason' => 'Project postponed or canceled'
                    ]
                ];

                foreach ($lead_cancel_reasons as $lead_cancel_reason) {
                    LeadCancelReason::create($lead_cancel_reason);
                }

                $order->update(['emp_id' => $employee->emp_id]);
            } else {

                $client = CompanyClient::where([
                    'iStatus' => 1,
                    'isDeleted' => 0,
                    'company_id' => $employee->company_id
                ])->first();

                $daysToAdd = $order->duration_in_days ?? 30;

                if ($client->subscription_end_date && \Carbon\Carbon::parse($client->subscription_end_date)->gt(now())) {
                    // Extend the existing subscription
                    $client->subscription_end_date = \Carbon\Carbon::parse($client->subscription_end_date)->addDays($daysToAdd);
                } else {
                    // Start a new subscription
                    $client->subscription_start_date = now();
                    $client->subscription_end_date   = now()->addDays($daysToAdd);
                }

                // update plan meta if you want
                $client->plan_amount = $order->amount;
                $client->plan_days   = $order->duration_in_days;

                $client->save();

                $order->update(['emp_id' => $employee->emp_id]);
            }

            // ✉️ Send different email for new vs existing
            $SendEmailDetails = DB::table('sendemaildetails')->where('id', 8)->first();
            $baseData = [
                'FromMail'  => $SendEmailDetails->strFromMail,
                'Title'     => $SendEmailDetails->strTitle,
                'ToEmail'   => $order->email,
                'Subject'   => $isNew ? 'Welcome to Salexo — Your Account Details' : 'Salexo — Subscription Updated',
            ];

            if ($isNew) {
                // New employee → send credentials
                $MailData = [
                    'Order'     => $order,
                    'Password'  => $plainPassword,
                    'Employee'  => $employee,
                    'Client'    => $client,
                    'AppUrl'    => config('app.app_url') ?? url('/'),
                ];

                Mail::send('emails.registration_detail', ['MailData' => $MailData], function ($message) use ($baseData) {
                    $message->from($baseData['FromMail'], $baseData['Title']);
                    $message->to($baseData['ToEmail'])->subject($baseData['Subject']);
                });
            } else {
                // Existing employee → send subscription extension/activation info (NO password)
                $MailData = [
                    'Order'     => $order,
                    'Employee'  => $employee,
                    'Client'    => $client,
                    'Start'     => \Carbon\Carbon::parse($client->subscription_start_date)->format('d M Y'),
                    'End'       => \Carbon\Carbon::parse($client->subscription_end_date)->format('d M Y'),
                    'Days'      => $order->duration_in_days,
                    'AppUrl'    => config('app.app_url') ?? url('/'),
                ];

                Mail::send('emails.subscription_updated', ['MailData' => $MailData], function ($message) use ($baseData) {
                    $message->from($baseData['FromMail'], $baseData['Title']);
                    $message->to($baseData['ToEmail'])->subject($baseData['Subject']);
                });
            }

            // $MailData = [
            //     'FromMail' => $SendEmailDetails->strFromMail,
            //     'Title' => $SendEmailDetails->strTitle,
            //     'ToEmail' => $order->email,
            //     'Subject' => $SendEmailDetails->strSubject,
            //     'Password' => $plainPassword, // optionally send in email
            //     'Order'    => $order,
            //     'Employee' => $employee
            // ];

            // Mail::send('emails.registration_detail', ['MailData' => $MailData], function ($message) use ($MailData) {
            //     $message->from($MailData['FromMail'], $MailData['Title']);
            //     $message->to($MailData['ToEmail'])->subject($MailData['Subject']);
            // });

            // return back();
            // return redirect()->route('razorpay.thank_you');

            // ✅ Flash success message to session
            return redirect()->route('front.index')->with('payment_success', true);
        } catch (\Throwable $e) {
            Log::error("RazorpayController@payment_success failed for Order ID: $id", [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return redirect()->route('front.registration')->with('error', 'Something went wrong while processing your order.');
        }
    }

    public function RazorFail()
    {
        try {

            return redirect()->route('front.index')->with('payment_fail', true);
        } catch (\Throwable $e) {
            Log::error('RazorpayController@RazorFail failed', [
                'message' => $e->getMessage(),
            ]);
            abort(500);
        }
    }

    public function thank_you(Request $request)
    {
        try {
            return view('thankyouPage');
        } catch (\Throwable $e) {
            Log::error('RazorpayController@thank_you failed', [
                'message' => $e->getMessage(),
            ]);
            abort(500);
        }
    }

    public function payment_cancel_by_user(Request $request)
    {
        try {
            $orderId = $request->orderId;

            Payment::where('oid', $orderId)->update([
                'status' => 'Fail',
                'Remarks' => 'Payment window closed',
            ]);

            return response()->json(['status' => 'fail']);
        } catch (\Throwable $e) {
            Log::error('RazorpayController@payment_cancel_by_user failed', [
                'message' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json(['status' => 'fail']);
        }
    }
}
