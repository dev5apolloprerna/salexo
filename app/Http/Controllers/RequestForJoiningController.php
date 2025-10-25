<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Plan;
use App\Models\RequestForJoining;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RequestForJoiningController extends Controller
{

    public function request_for_joining()
    {

        try {

            $states = State::pluck('stateName', 'stateId')->toArray();
            $plans = Plan::pluck('plan_name', 'plan_id')->toArray();

            $planDetails = Plan::all()->keyBy('plan_id')->map(function ($plan) {
                return [
                    'amount' => $plan->plan_amount,
                    'days' => $plan->plan_days,
                ];
            });

            return view('request_for_joining.create', compact('states', 'planDetails', 'plans'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function request_for_joining_store(Request $request)
    {
        $request->validate([
            'email' => 'required|unique:company_client_master,email',
            'GST' => [
                'nullable', // or 'required' if mandatory
                'string',
                'max:100',
                'regex:/^(\d{2}[A-Z]{5}\d{4}[A-Z]{1}[A-Z\d]{1}[Z]{1}[A-Z\d]{1})$/'
            ],

        ], [
            'email.required' => 'Email is required.',
            'email.unique' => 'This email already exists.',
        ]);

        try {

            $data = $request->all();
            $data['subscription_start_date'] = Carbon::now();

            // Get the plan days
            $planDays = isset($request->plan_days) ? (int)$request->plan_days : 0;

            // Calculate subscription end date
            $data['subscription_end_date'] = Carbon::now()->addDays($planDays);

            // Hash the password if provided
            if (!empty($request->password)) {
                $data['password'] = Hash::make($request->password);
            }

            RequestForJoining::create($data);

            $SendEmailDetails = DB::table('sendemaildetails')
                ->where(['id' => 4])
                ->first();

            $msg = array(
                'FromMail' => $SendEmailDetails->strFromMail,
                'Title' => $SendEmailDetails->strTitle,
                'ToEmail' => "dev2.apolloinfotech@gmail.com",
                'Subject' => $SendEmailDetails->strSubject
            );

            Mail::send('emails.request_for_joining', ['data' => $data], function ($message) use ($msg) {
                $message->from($msg['FromMail'], $msg['Title']);
                $message->to($msg['ToEmail'])->subject($msg['Subject']);
            });

            return redirect()->route('thank.you');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
