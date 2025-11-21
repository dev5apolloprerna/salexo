<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\CompanyClient;
use App\Models\DealDone;
use App\Models\LeadCancelReason;
use App\Models\LeadHistory;
use App\Models\LeadMaster;
use App\Models\LeadPipeline;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EmployeeHomeController extends Controller
{

    public function index()
    {
        try {
            $emp = Auth::guard('web_employees')->user();
            $emp_id = $emp->company_id;

            // $piplines = LeadPipeline::select(

            //     'lead_pipeline_master.pipeline_id',
            //     'lead_pipeline_master.pipeline_name',
            //     'lead_pipeline_master.color',
            //     'lead_pipeline_master.icon',
            //     'lead_pipeline_master.created_at',
            //     'lead_pipeline_master.company_id',
            //     DB::raw('COUNT(lead_master.lead_id) as status_count')

            // )
            //     ->leftJoin('lead_master', function ($join) use ($emp) {
            //         $join->on('lead_master.status', '=', 'lead_pipeline_master.pipeline_id')
            //             ->where('lead_master.iCustomerId', $emp->company_id)
            //             ->where('lead_master.iEnterBy', $emp->emp_id)
            //             ->where('lead_master.isDelete', 0);
            //     })
            //     ->where('lead_pipeline_master.company_id', $emp->company_id)
            //     ->groupBy(
            //         'lead_pipeline_master.pipeline_id',
            //         'lead_pipeline_master.pipeline_name',
            //         'lead_pipeline_master.color',
            //         'lead_pipeline_master.icon',
            //         'lead_pipeline_master.created_at',
            //         'lead_pipeline_master.company_id'
            //     )
            //     ->get();

            $pipline = LeadPipeline::select(

                'lead_pipeline_master.pipeline_id',
                'lead_pipeline_master.pipeline_name',
                'lead_pipeline_master.color',
                'lead_pipeline_master.icon',
                'lead_pipeline_master.created_at',
                'lead_pipeline_master.company_id',
                DB::raw('COUNT(lead_master.lead_id) as status_count')

            )
                ->leftJoin('lead_master', function ($join) use ($emp) {
                    $join->on('lead_master.status', '=', 'lead_pipeline_master.pipeline_id')
                        ->where('lead_master.iCustomerId', $emp->company_id)
                        ->where('lead_master.employee_id', $emp->emp_id)
                        ->where('lead_master.isDelete', 0);
                })
                ->where('lead_pipeline_master.company_id', $emp->company_id)
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
                ->leftJoin('deal_done', function ($join) use ($emp) {
                    $join->on('deal_done.status', '=', 'lead_pipeline_master.pipeline_id')
                        ->where('deal_done.iCustomerId', $emp->company_id)
                        ->where('deal_done.iEnterBy', $emp->emp_id)
                        ->where('deal_done.isDelete', 0);
                })
                ->where('lead_pipeline_master.company_id', $emp->company_id)
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
                ->leftJoin('deal_cancel', function ($join) use ($emp) {
                    $join->on('deal_cancel.status', '=', 'lead_pipeline_master.pipeline_id')
                        ->where('deal_cancel.iCustomerId', $emp->company_id)
                        ->where('deal_cancel.iEnterBy', $emp->emp_id)
                        ->where('deal_cancel.isDelete', 0);
                })

                ->where('lead_pipeline_master.company_id', $emp->company_id)
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
            // dd($piplines);

            $allLeads = LeadMaster::where([
                'iCustomerId' => $emp->company_id,
                'employee_id' => $emp->emp_id
            ])
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

            return view('employee.home', compact('emp_id', 'piplines', 'todays_followup_count', 'overdues_followup_count', 'employees', 'topProducts'));
        } catch (\Exception $e) {
            Log::error('Error in HomeController@index: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function getProfile()
    {
        try {

            $session = Auth::user()->emp_id;

            $users = Employee::where('employee_master.emp_id',  $session)->first();

            return view('company_client.profile', compact('users'));
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

        $user_role_id = session()->get('user_role_id');



        $userId = session()->get('emp_id');

        $company_id = session()->get('company_id');

        #Validations

        $request->validate([

            'emp_name'   => 'required',

            'emp_email'  => 'required|email|unique:employee_master,emp_email,' . $userId . ',emp_id',

            'emp_mobile' => 'required|numeric|digits:10',

            'emp_loginId' => 'required',

        ]);



        try {

            DB::beginTransaction();



            if ($user_role_id == 2) {

                Employee::where(['emp_id' => $userId])->update([

                    'emp_name' => $request->emp_name,

                    'emp_email' => $request->emp_email,

                    'emp_mobile' => $request->emp_mobile,

                    'emp_loginId' => $request->emp_loginId,

                ]);



                CompanyClient::where(['company_id' => $company_id, 'email' => $userId])->update([

                    'company_name' => $request->emp_name,

                    'email' => $request->emp_email,

                    'mobile' => $request->emp_mobile,

                ]);
            }



            #Commit Transaction

            DB::commit();



            #Return To Profile page with success

            return back()->with('success', 'Profile Updated Successfully.');
        } catch (\Throwable $th) {

            DB::rollBack();

            return back()->with('error', $th->getMessage());
        }
    }

    public function changePassword(Request $request)

    {

        $session = Auth::user()->empid;



        $role = auth()->user()->role_id;





        $user = Employee::where('empid', '=', $session)->where(['iStatus' => 1])->first();



        if (Hash::check($request->current_password, $user->password)) {

            $newpassword = $request->new_password;

            $confirmpassword = $request->new_confirm_password;



            if ($newpassword == $confirmpassword) {

                $User = DB::table('employee_master')

                    ->where(['iStatus' => 1, 'empid' => $session])

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

    public function todays_followup(Request $request)
    {
        try {

            $leadPipeline = LeadPipeline::all();
            $leadCancelList = LeadCancelReason::all();
            $search = request('search');

            $leads = LeadMaster::where([
                'lead_master.iStatus' => 1,
                'lead_master.isDelete' => 0,
                'lead_master.iCustomerId' => Auth::user()->company_id,
                'lead_master.employee_id' => Auth::user()->emp_id
            ])
                ->leftJoin('service_master', 'lead_master.product_service_id', '=', 'service_master.service_id')
                ->leftJoin('lead_source_master', 'lead_master.LeadSourceId', '=', 'lead_source_master.lead_source_id')
                ->select(
                    'lead_master.*',
                    'service_master.service_name',
                    'lead_source_master.lead_source_name'
                )->when($search, function ($query, $search) {
                        return $query->where(function ($q) use ($search) {
                            $q->where('lead_master.company_name', 'like', '%' . $search . '%')
                            ->orWhere('lead_master.customer_name', 'like', '%' . $search . '%');
                        });
                    })
                ->get();
            $todaysFollowups = $leads->filter(function ($lead) {
                try {
                    $date = Carbon::createFromFormat('d-m-Y h:i A', trim($lead->next_followup_date));
                    return $date->isToday();
                } catch (\Exception $e) {
                    return false;
                }
            });

            // Paginate manually (you can skip this if listing all is okay)
            $page = request('page', 1);
            $perPage = env('PER_PAGE_COUNT', 10);
            $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $todaysFollowups->forPage($page, $perPage),
                $todaysFollowups->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
            // return view('company_client.follow_up.todays_followup', compact(
            //     'paginated',
            //     'leadPipeline',
            //     'leadCancelList'

            // ));
            return view('employee.follow_up.todays_followup', compact(
                'paginated',
                'leadPipeline',
                'leadCancelList'

            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function over_due_followup(Request $request)
    {

        try {
            $search = request('search');
            $leads = LeadMaster::where([
                'lead_master.iStatus' => 1,
                'lead_master.isDelete' => 0,
                'lead_master.iCustomerId' => Auth::user()->company_id,
                'lead_master.employee_id' => Auth::user()->emp_id
            ])
                ->leftJoin('service_master', 'lead_master.product_service_id', '=', 'service_master.service_id')
                ->leftJoin('lead_source_master', 'lead_master.LeadSourceId', '=', 'lead_source_master.lead_source_id')
                ->select(
                    'lead_master.*',
                    'service_master.service_name',
                    'lead_source_master.lead_source_name'
                )->when($search, function ($query, $search) {
                        return $query->where(function ($q) use ($search) {
                            $q->where('lead_master.company_name', 'like', '%' . $search . '%')
                            ->orWhere('lead_master.customer_name', 'like', '%' . $search . '%');
                        });
                    })
                ->get();
            $over_due_Followups = $leads->filter(function ($lead) {
                try {
                    $date = Carbon::createFromFormat('d-m-Y h:i A', trim($lead->next_followup_date));
                    return $date->lt(today());
                } catch (\Exception $e) {
                    return false;
                }
            });

            // Paginate manually
            $page = request('page', 1);
            $perPage = env('PER_PAGE_COUNT', 10);
            $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $over_due_Followups->forPage($page, $perPage),
                $over_due_Followups->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
            //return view('company_client.follow_up.over_due_followup', compact('paginated'));
            return view('employee.follow_up.over_due_followup', compact('paginated'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function status(Request $request, $status)
    {
        try {
            $emp = Auth::guard('web_employees')->user();
            $leadPipeline = LeadPipeline::where(['slugname' => $status])->first();
            if (!$leadPipeline) {
                return redirect()->back()->with('error', 'Invalid pipeline status provided.');
            }
            $leadPipeline = $leadPipeline->pipeline_name;

            // $leads = LeadMaster::where([
            //     'lead_master.iStatus' => 1,
            //     'lead_master.isDelete' => 0,
            //     'lead_master.iCustomerId' => $emp->company_id,
            //     'lead_master.iEnterBy' => $emp->emp_id,
            // ])
            //     ->whereIn('lead_master.status', function ($query) use ($emp, $leadPipeline) {
            //         $query->select('pipeline_id')
            //             ->from('lead_pipeline_master')
            //             ->where('company_id', $emp->company_id)
            //             ->where('pipeline_name', 'like', $leadPipeline);
            //     })
            //     ->join('service_master', 'lead_master.product_service_id', '=', 'service_master.service_id')
            //     ->join('lead_source_master', 'lead_master.LeadSourceId', '=', 'lead_source_master.lead_source_id')
            //     ->select(
            //         'lead_master.*',
            //         'service_master.service_name',
            //         'lead_source_master.lead_source_name'
            //     )
            //     ->paginate(10);
            $search = request('search');
            if ($status === 'deal-done') {
                // Get leads from `deal_done` table
                $leads = DB::table('deal_done')
                    ->where([
                        ['deal_done.iStatus', '=', 1],
                        ['deal_done.isDelete', '=', 0],
                        ['deal_done.iCustomerId', '=', $emp->company_id],
                        ['deal_done.iEnterBy', '=', $emp->emp_id],
                    ])
                    ->whereIn('deal_done.status', function ($query) use ($emp, $leadPipeline) {
                        $query->select('pipeline_id')
                            ->from('lead_pipeline_master')
                            ->where('company_id', $emp->company_id)
                            ->where('pipeline_name', 'like', $leadPipeline);
                    })
                    ->leftJoin('service_master', 'deal_done.product_service_id', '=', 'service_master.service_id')
                    ->leftJoin('lead_source_master', 'deal_done.LeadSourceId', '=', 'lead_source_master.lead_source_id')
                    ->select(
                        'deal_done.*',
                        'service_master.service_name',
                        'lead_source_master.lead_source_name'
                    )->when($search, function ($query, $search) {
                        return $query->where(function ($q) use ($search) {
                            $q->where('deal_done.company_name', 'like', '%' . $search . '%')
                            ->orWhere('deal_done.customer_name', 'like', '%' . $search . '%');
                        });
                    })
                    ->paginate(env('PER_PAGE_COUNT', 10));
            } elseif ($status === 'deal-cancel') {
                // Get leads from `deal_cancel` table
                $leads = DB::table('deal_cancel')
                    ->where([
                        ['deal_cancel.iStatus', '=', 1],
                        ['deal_cancel.isDelete', '=', 0],
                        ['deal_done.iCustomerId', '=', $emp->company_id],
                        ['deal_done.iEnterBy', '=', $emp->emp_id],
                    ])
                    ->whereIn('deal_cancel.status', function ($query) use ($emp, $leadPipeline) {
                        $query->select('pipeline_id')
                            ->from('lead_pipeline_master')
                            ->where('company_id', $emp->company_id)
                            ->where('pipeline_name', 'like', $leadPipeline);
                    })
                    ->leftJoin('service_master', 'deal_cancel.product_service_id', '=', 'service_master.service_id')
                    ->leftJoin('lead_source_master', 'deal_cancel.LeadSourceId', '=', 'lead_source_master.lead_source_id')
                    ->select(
                        'deal_cancel.*',
                        'service_master.service_name',
                        'lead_source_master.lead_source_name'
                    )->when($search, function ($query, $search) {
                        return $query->where(function ($q) use ($search) {
                            $q->where('deal_cancel.company_name', 'like', '%' . $search . '%')
                            ->orWhere('deal_cancel.customer_name', 'like', '%' . $search . '%');
                        });
                    })
                    ->paginate(env('PER_PAGE_COUNT', 10));
            } else {
                // Get leads from `lead_master` table
                $leads = LeadMaster::where([
                    'lead_master.iStatus' => 1,
                    'lead_master.isDelete' => 0,
                    'lead_master.iCustomerId' => $emp->company_id,
                    'lead_master.employee_id' => $emp->emp_id
                ])
                    ->whereIn('lead_master.status', function ($query) use ($emp, $leadPipeline) {
                        $query->select('pipeline_id')
                            ->from('lead_pipeline_master')
                            ->where('company_id', $emp->company_id)
                            ->where('pipeline_name', 'like', $leadPipeline);
                    })
                    ->leftJoin('service_master', 'lead_master.product_service_id', '=', 'service_master.service_id')
                    ->leftJoin('lead_source_master', 'lead_master.LeadSourceId', '=', 'lead_source_master.lead_source_id')
                    ->select(
                        'lead_master.*',
                        'service_master.service_name',
                        'lead_source_master.lead_source_name'
                    )->when($search, function ($query, $search) {
                        return $query->where(function ($q) use ($search) {
                            $q->where('lead_master.company_name', 'like', '%' . $search . '%')
                            ->orWhere('lead_master.customer_name', 'like', '%' . $search . '%');
                        });
                    })
                    ->paginate(env('PER_PAGE_COUNT', 10));
            }

            return view('employee.follow_up.new_leads', compact('leads', 'status'));
        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function followup_detail($status, $id)
    {
        $user = Auth::user();
        $lead = LeadMaster::findOrFail($id);
        $leadPipeline = LeadPipeline::where(['company_id' => $user->company_id])->get();
        $leadCancelList = LeadCancelReason::where(['company_id' => $user->company_id])->get();
        
        $lead = LeadMaster::select(
            'lead_master.*',
            'lead_source_master.lead_source_name',
            'service_master.service_name'
        )
            ->where('lead_master.lead_id', $id)
            ->leftjoin('lead_source_master', 'lead_master.LeadSourceId', '=', 'lead_source_master.lead_source_id')
            ->leftjoin('service_master', 'lead_master.product_service_id', '=', 'service_master.service_id')
            ->first();

        if ($status == "deal-done") {
            $lead = DealDone::select(
                'deal_done.*',
                'lead_source_master.lead_source_name',
                'service_master.service_name',
            )
                ->where('deal_done.lead_id', $id)
                ->leftjoin('lead_source_master', 'deal_done.LeadSourceId', '=', 'lead_source_master.lead_source_id')
                ->leftjoin('service_master', 'deal_done.product_service_id', '=', 'service_master.service_id')
                ->first();
        }
        if ($status == "deal-cancel") {
            $lead = DealCancel::select(
                'deal_cancel.*',
                'lead_source_master.lead_source_name',
                'service_master.service_name',
            )
                ->where('deal_cancel.lead_id', $id)
                ->leftjoin('lead_source_master', 'deal_cancel.LeadSourceId', '=', 'lead_source_master.lead_source_id')
                ->leftjoin('service_master', 'deal_cancel.product_service_id', '=', 'service_master.service_id')
                ->first();
        }

        $lead_history = LeadHistory::where([
            'lead_history.iCustomerId' => Auth::user()->company_id,
            'lead_history.iLeadId' => $id
        ])
            ->select(
                'lead_history.*',
                'lead_pipeline_master.pipeline_name',
                'lead_cancel_reason.reason',
                'service_master.service_name',
                'lead_source_master.lead_source_name'
            )
            ->orderBY('iLeadHistoryId', 'desc')
            ->leftJoin('lead_master', 'lead_history.iLeadId', '=', 'lead_master.lead_id')
            ->leftJoin('service_master', 'lead_master.product_service_id', '=', 'service_master.service_id')
            ->leftJoin('lead_pipeline_master', 'lead_history.status', '=', 'lead_pipeline_master.pipeline_id')
            ->leftjoin('lead_cancel_reason', 'lead_history.cancel_reason_id', '=', 'lead_cancel_reason.lead_cancel_reason_id')
            ->leftJoin('lead_source_master', 'lead_master.LeadSourceId', '=', 'lead_source_master.lead_source_id')

            ->paginate(env('PER_PAGE_COUNT', 10));

        return view('employee.follow_up.followup-detail', compact('lead', 'leadPipeline', 'leadCancelList', 'lead_history', 'id','status'));
    }

    public function followup_update(Request $request)
    {
        // dd($request);
        // try {
$user = Auth::guard('web_employees')->user();
        $request->validate([
            'status' => 'required',
            'cancel_reason_id' => 'nullable|exists:lead_cancel_reason,lead_cancel_reason_id',
            'followup_datetime' => 'nullable',
            'amount' => 'nullable|numeric',
            'comment' => 'required',
        ]);

        $emp = Auth::guard('web_employees')->user();
        $lead = LeadMaster::findOrFail($request->lead_id);
        
        $lead_pipeline = LeadPipeline::where([
                'company_id' => $user->company_id,
                'pipeline_id' => $request->status
            ])->first();

        $leadHistoryData = [
            'iLeadId' => $request->lead_id ?? 0,
            'iCustomerId' => $lead->iCustomerId ?? 0,
            'Comments' => $request->comment,
            'followup_by' => $emp->emp_id ?? 0,
            'next_followup_date' => $request->followup_datetime,
            'status' => $request->status ?? 0,
            'cancel_reason_id' => $request->cancel_reason_id ?? 0,
            'amount' => $request->amount ?? 0,
            'created_at' => now(),
        ];

        $leadHistory = LeadHistory::create($leadHistoryData);

        $lead->lead_history_id = $leadHistory->id;
        $lead->comments = $request->comment;
        $lead->followup_by = $emp->emp_id ?? 0;
        $lead->next_followup_date = $request->followup_datetime;
        $lead->status = $request->status ?? 0;
        $lead->cancel_reason_id = $request->cancel_reason_id ?? 0;
        $lead->amount = $request->amount ?? 0;
        $lead->iEnterBy = $emp->emp_id;
        $lead->save();
        
        if ($lead_pipeline && $lead_pipeline->slugname === "deal-done") {
            $dealDoneData = $lead->toArray();
            $dealDoneData['deal_done_at'] = now();
            DealDone::create($dealDoneData);
            $lead->delete();
        } else if ($lead_pipeline && $lead_pipeline->slugname === "deal-cancel") {
            $dealCancelData = $lead->toArray();
            $dealCancelData['deal_cancel_at'] = now(); // Set the deal cancel date
            DealCancel::create($dealCancelData);
            $lead->delete();
        }

        if ($request->lead_status == 'over-due') {
            return redirect()->route('clients.over_due_followup')->with('success', 'Follow-up updated successfully.');
        } else if ($request->lead_status == 'todays-followup') {
            return redirect()->route('clients.todays_followup')->with('success', 'Follow-up updated successfully.');
        }

        // return redirect()->back()->with('success', 'Follow-up updated successfully.');
        return redirect()->route('employee.status', $request->lead_status)->with('success', 'Follow-up updated successfully.');

        // } catch (\Exception $e) {

        //     return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());

        // }

    }
}
