<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Employee;
use App\Models\CompanyClient;
use App\Models\DealDone;
use App\Models\LeadMaster;
use App\Models\LeadPipeline;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;

class CompanyClientHomeController extends Controller
{

    public function index()
    {

        try {
            $emp_id = Auth::guard('web_employees')->user()->company_id;

            $pipline = LeadPipeline::select(

                'lead_pipeline_master.pipeline_id',
                'lead_pipeline_master.pipeline_name',
                'lead_pipeline_master.color',
                'lead_pipeline_master.icon',
                'lead_pipeline_master.created_at',
                'lead_pipeline_master.company_id',
                DB::raw('COUNT(lead_master.lead_id) as status_count')

            )
                ->leftJoin('lead_master', function ($join) use ($emp_id) {
                    $join->on('lead_master.status', '=', 'lead_pipeline_master.pipeline_id')
                        ->where('lead_master.iCustomerId', $emp_id)
                        ->where('lead_master.isDelete', 0);
                })
                ->where('lead_pipeline_master.company_id', $emp_id)
                ->whereNotIn('lead_pipeline_master.slugname', ['deal-done', 'deal-cancel'])
                ->groupBy(

                    'lead_pipeline_master.pipeline_id',
                    'lead_pipeline_master.pipeline_name',
                    'lead_pipeline_master.color',
                    'lead_pipeline_master.icon',
                    'lead_pipeline_master.created_at',
                    'lead_pipeline_master.company_id'

                );

            $piplineDones = LeadPipeline::select(

                'lead_pipeline_master.pipeline_id',
                'lead_pipeline_master.pipeline_name',
                'lead_pipeline_master.color',
                'lead_pipeline_master.icon',
                'lead_pipeline_master.created_at',
                'lead_pipeline_master.company_id',
                DB::raw('COUNT(deal_done.lead_id) as status_count')

            )
                ->leftJoin('deal_done', function ($join) use ($emp_id) {
                    $join->on('deal_done.status', '=', 'lead_pipeline_master.pipeline_id')
                        ->where('deal_done.iCustomerId', $emp_id)
                        ->where('deal_done.isDelete', 0);
                })
                ->where('lead_pipeline_master.company_id', $emp_id)
                ->whereIn('lead_pipeline_master.slugname', ['deal-done'])
                ->groupBy(

                    'lead_pipeline_master.pipeline_id',
                    'lead_pipeline_master.pipeline_name',
                    'lead_pipeline_master.color',
                    'lead_pipeline_master.icon',
                    'lead_pipeline_master.created_at',
                    'lead_pipeline_master.company_id'

                );

            $piplineCancels = LeadPipeline::select(

                'lead_pipeline_master.pipeline_id',
                'lead_pipeline_master.pipeline_name',
                'lead_pipeline_master.color',
                'lead_pipeline_master.icon',
                'lead_pipeline_master.created_at',
                'lead_pipeline_master.company_id',
                DB::raw('COUNT(deal_cancel.lead_id) as status_count')

            )
                ->leftJoin('deal_cancel', function ($join) use ($emp_id) {
                    $join->on('deal_cancel.status', '=', 'lead_pipeline_master.pipeline_id')
                        ->where('deal_cancel.iCustomerId', $emp_id)
                        ->where('deal_cancel.isDelete', 0);
                })
                ->where('lead_pipeline_master.company_id', $emp_id)
                ->whereIn('lead_pipeline_master.slugname', ['deal-cancel'])
                ->groupBy(

                    'lead_pipeline_master.pipeline_id',
                    'lead_pipeline_master.pipeline_name',
                    'lead_pipeline_master.color',
                    'lead_pipeline_master.icon',
                    'lead_pipeline_master.created_at',
                    'lead_pipeline_master.company_id'

                );

            $piplines = $pipline->union($piplineDones)->union($piplineCancels)->get();

            $allLeads = LeadMaster::where('iCustomerId', $emp_id)
                ->where('iStatus', 1)
                ->where('isDelete', 0)
                ->get();

            $todays_followup_count = $allLeads->filter(function ($lead) {
                try {
                    if (!$lead->next_followup_date) return false;
                    $date = \Carbon\Carbon::createFromFormat('d-m-Y h:i A', trim($lead->next_followup_date));
                    return $date->isToday();
                } catch (\Exception $e) {
                    return false;
                }
            })->count();

            $overdues_followup_count = $allLeads->filter(function ($lead) {

                try {

                    if (!$lead->next_followup_date) return false;



                    $date = \Carbon\Carbon::createFromFormat('d-m-Y h:i A', trim($lead->next_followup_date));

                    return $date->lt(today());
                } catch (\Exception $e) {

                    return false;
                }
            })->count();

            $employees = Employee::orderBy('emp_name', 'asc')
                ->where([
                    'isDelete' => 0,
                    'isCompanyAdmin' => 0,
                    'company_id' => Auth::guard('web_employees')->user()->company_id
                ])
                ->get();

            $lead_pipeline = LeadPipeline::where([
                'company_id' => $emp_id,
                'pipeline_name' => "Deal Done"
            ])->first();

            $topProducts = DealDone::select(
                'service_master.service_name',
                DB::raw('COUNT(deal_done.lead_id) as quantity'),
                DB::raw('SUM(deal_done.amount) as total_value')
            )
                ->leftJoin('service_master', 'service_master.service_id', '=', 'deal_done.product_service_id')
                ->where([
                    'deal_done.iCustomerId' => $emp_id,
                    'deal_done.status' => $lead_pipeline->pipeline_id
                ])
                ->where('deal_done.isDelete', 0)
                ->groupBy('deal_done.product_service_id', 'service_master.service_name')
                ->get();

            return view('company_client.home', compact('emp_id', 'piplines', 'todays_followup_count', 'overdues_followup_count', 'employees', 'topProducts'));
        } catch (\Exception $e) {
            Log::error('Error in HomeController@index: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function getProfile()
    {
        try {

            $session = Auth::user()->emp_id;
            $company_id = Auth::user()->company_id;

            $users = Employee::where('employee_master.emp_id',  $session)->first();
            $users1 = CompanyClient::where('company_id',  $company_id)->first();

            return view('company_client.profile', compact('users','users1'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function EditProfile()
    {

        try {

            $roles = Role::where('id', '!=', '1')->get();

            return view('company_client.Editprofile', compact('roles'));
        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updateProfile(Request $request)
    {
        $user_role_id = Auth::user()->role_id;
         $userId =  Auth::user()->emp_id;
         $company_id =  Auth::user()->company_id;

        // Validations
        $request->validate([
            'emp_name'     => 'required',
            'emp_email'    => 'nullable|email|unique:employee_master,emp_email,' . $userId . ',emp_id',
            'emp_mobile'   => 'required|numeric|digits:10',
            'emp_loginId'  => 'nullable',
            // logo is optional
            'company_logo' => 'nullable|image|mimes:png,jpg,jpeg,webp,gif|max:3072', // 3MB
        ]);

        try {
            DB::beginTransaction();

            if ((int)$user_role_id === 2) {
                // Update employee basic info
                Employee::where(['emp_id' => $userId])->update([
                    'emp_name'   => $request->emp_name,
                    'emp_email'  => $request->emp_email,
                    'emp_mobile' => $request->emp_mobile,
                    'emp_loginId'=> $request->emp_loginId,
                ]);

                // Fetch company (to read old logo path and then update)
                $company = CompanyClient::where(['company_id' => $company_id])->firstOrFail();

                // Prepare fields to update
                $companyUpdate = [
                    // NOTE: you were setting company_name = emp_name before; keeping same behavior
                    'contact_person_name'=> $request->emp_name,
                    'email'            => $request->emp_email,
                    'mobile'           => $request->emp_mobile,
                    'GST'           => $request->GST,
                    'payment_terms'    => $request->payment_terms,
                    'delivery_terms'   => $request->delivery_terms,
                    'terms_condition'  => $request->terms_condition,
                ];

                // ---- Handle company logo upload (optional) ----
                if ($request->hasFile('company_logo')) {
                    $file = $request->file('company_logo');

                    // Destination: public_html/uploads/company
                    $destAbs = public_path('../uploads/company'); // public_path() -> public_html on live
                    if (!File::isDirectory($destAbs)) {
                        File::makeDirectory($destAbs, 0775, true);
                    }

                    $ext     = strtolower($file->getClientOriginalExtension());
                    $fname   = 'company_' . (int)$company_id . '_' . date('Ymd_His') . '.' . $ext;
                    $file->move($destAbs, $fname);

                    $newRelPath = 'uploads/company/' . $fname; // relative path for asset()

                    // Delete old logo if exists
                    if (!empty($company->company_logo)) {
                        $oldAbs = public_path(ltrim($company->company_logo, '/\\'));
                        if (File::exists($oldAbs)) {
                            @File::delete($oldAbs);
                        }
                    }

                    // Put new path into update array
                    $companyUpdate['company_logo'] = $newRelPath;
                }

                // Commit company updates
                CompanyClient::where(['company_id' => $company_id])->update($companyUpdate);
                 if (!empty($request->terms_condition)) 
                 {
                    $raw   = $request->terms_condition;

                    // Replace <li> & <br> with line breaks, then strip remaining tags
                    $normalized = str_ireplace(
                        ['</li>', '<li>', '<br>', '<br/>', '<br />', '</p>', '<p>', '</ul>', '<ul>'],
                        ["\n",   '',    "\n",   "\n",    "\n",     "\n",  '',    "\n",   ''],
                        $raw
                    );

                $plainTerms = trim(strip_tags($normalized));

                if ($plainTerms !== '') {
                    // Update existing termcondition rows for this company
                    DB::table('termcondition')
                        ->where([
                            'companyID' => $company_id,
                            'isDelete'  => 0,
                        ])
                        ->update([
                            'description' => $plainTerms,
                            'updated_at'    => now(),
                        ]);

                    }
                }

            }

            DB::commit();
            return back()->with('success', 'Profile Updated Successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }


    public function changePassword(Request $request)

    {

        $session = Auth::user()->emp_id;

        $role = auth()->user()->role_id;

        $user = Employee::where('emp_id', '=', $session)->where(['iStatus' => 1])->first();
        

        if (Hash::check($request->current_password, $user->password)) {

            $newpassword = $request->new_password;

            $confirmpassword = $request->new_confirm_password;


            if ($newpassword == $confirmpassword) {

                $User = DB::table('employee_master')

                    ->where(['iStatus' => 1, 'emp_id' => $session])

                    ->update([

                        'password' => Hash::make($confirmpassword),

                    ]);



                Auth::logout();

                $request->session()->forget('name');

                $request->session()->forget('user_role_id');

                $request->session()->forget('userId');

                return redirect()->route('user_login')->with('success', 'Your password has been successfully changed!');



                // return back()->with('success', 'User Password Updated Successfully.');

            } else {

                return back()->with('error', 'password and confirm password does not match');
            }
        } else {

            return back()->with('error', 'Current Password does not match');
        }
    }

    public function password_forgot(Request $request)
    {
        return view('forgot_pass');
    }

    public function PasswordForgot(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10'
        ]);

        try {

            $mobile = trim($request->mobile);

            $employee = Employee::where([
                'emp_mobile' => $mobile,
                'iStatus' => 1,
                'isDelete' => 0
            ])->first();

            if (empty($employee)) {
                return back()->with('error', 'Mobile number is not registered.');
            }

            $token = Str::random(64);

            // Insert token into password_resets table
            DB::table('password_resets')->updateOrInsert(
                [
                    'email' => $employee->emp_mobile
                ],
                [
                    'email' => $employee->emp_mobile,
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]
            );

            // Create password reset link            
            $resetLink = route('password.reset', ['token' => $token, 'mobile' => $mobile]);


            // WhatsApp API credentials
            $whatsappToken = 'EAATZAZAlCLXjEBO3L964MCRbqZA8kRj95hjONF6DRUaZCkd3bk2LzHvKbvV72eZCqMEOjm9pVaEG9ZCvFd2m1GsxFkysBQPXYmVbE7HSVdrrut3PijBInprtr4KTwvPGbQw0b2AHlIpfgGyeKSOosoc05ztRw8W1y0hlZC84U4ZAW31CikzFYNjtKyc2FgQ03wqi4QZDZD'; // Replace with your WhatsApp API token
            $phoneNumberId = '658603253999245'; // Replace with your phone number ID

            $response = Http::withToken($whatsappToken)->post("https://graph.facebook.com/v19.0/{$phoneNumberId}/messages", [
                "messaging_product" => "whatsapp",
                "to" => "+91" . $mobile,
                // "to" => "+919725123569",
                "type" => "template",
                "template" => [
                    "name" => "forgot_password1",
                    "language" => [
                        "code" => "en"
                    ],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $employee->emp_name ?? 'User'
                                ],
                                [
                                    "type" => "text",
                                    "text" => "Vraj Group Of Dental Clinic"
                                ],
                            ]
                        ],
                        [
                            'type' => 'button',
                            'sub_type' => 'url',
                            'index' => 0,
                            'parameters' => [
                                [
                                    'type' => 'text',
                                    'text' => $resetLink
                                ],
                            ]
                        ]
                    ]
                ]
            ]);

            // Check if WhatsApp API call was successful
            if ($response->successful()) {
                // WhatsApp message sent successfully
                Log::info('WhatsApp message sent successfully to ' . $request->mobile_number);
            } else {
                // WhatsApp message failed to send
                Log::error('Failed to send WhatsApp message: ' . $response->body());
            }

            return back()->with('success', 'Password reset link sent to your WhatsApp number.');
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred: ' . $e->getMessage());

            // Optionally, you can return a specific error message or handle it differently
            return back()->with('error', 'An error occurred. Please try again later.');
        }
    }

    public function showResetForm(Request $request, $token)
    {
        try {
            $mobile = $request->query('mobile'); // gets ?mobile=9725123569

            return view('resetpasswordform', compact('token', 'mobile'));
        } catch (\Exception $e) {
            Log::error('Show Reset Form Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('FrontIndex')->with('error', 'Invalid reset link.');
        }
    }

    public function set_new_password_submit(Request $request)
    {
        try {
            // 1. Validate request
            $request->validate([
                'token' => 'required',
                'mobile' => 'required|digits:10|exists:employee_master,emp_mobile',
                'password' => 'required|min:6|confirmed',
            ]);

            // 2. Check if token and email match
            $tokenData = DB::table('password_resets')
                ->where('mobile', $request->mobile)
                ->where('token', $request->token)
                ->first();

            if (!$tokenData) {
                return back()->withErrors(['error' => 'Invalid or expired password reset token.']);
            }

            if (Carbon::parse($tokenData->created_at)->addMinutes(10)->isPast()) {
                return back()->with(['error' => 'This password reset link has expired. Please request a new one.']);
            }

            // 3. Reset the user's password
            Employee::where(['emp_mobile' => $request->mobile])->update([
                "password"  => Hash::make($request->password)
            ]);

            // 4. Delete password reset token
            DB::table('password_resets')->where('mobile', $request->mobile)->delete();

            // 6. Redirect to home/dashboard with success
            return redirect()->route('user_login')->with('success', 'Your password has been reset successfully!');
        } catch (\Exception $e) {
            Log::error('Reset Password Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Unable to reset password. Please try again later.');
        }
    }
}
