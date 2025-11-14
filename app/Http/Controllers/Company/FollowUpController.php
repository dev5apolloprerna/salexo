<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\DealCancel;
use App\Models\DealDone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\LeadCancelReason;
use App\Models\LeadHistory;
use App\Models\LeadMaster;
use App\Models\LeadPipeline;
use App\Models\LeadUdfData;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FollowUpController extends Controller
{

    public function todays_followup(Request $request)
    {
        try {

            $leadPipeline = LeadPipeline::all();
            $leadCancelList = LeadCancelReason::all();
            $search = request('search');
            $leads = LeadMaster::where([
                'lead_master.iStatus' => 1,
                'lead_master.isDelete' => 0,
                'lead_master.iCustomerId' => Auth::user()->company_id
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
            $perPage = config('app.per_page');
            $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $todaysFollowups->forPage($page, $perPage),
                $todaysFollowups->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return view('company_client.follow_up.todays_followup', compact(
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
                'lead_master.iCustomerId' => Auth::user()->company_id

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
            $perPage = config('app.per_page');
            $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $over_due_Followups->forPage($page, $perPage),
                $over_due_Followups->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
            return view('company_client.follow_up.over_due_followup', compact('paginated'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function new_lead(Request $request, $status)
    {
        try {
            $user = Auth::user();
            $companyId = $user->company_id;

            $pipeline = LeadPipeline::where('slugname', $status)->first();

            if (!$pipeline) {
                return redirect()->back()->with('error', 'Invalid pipeline status provided.');
            }

            $pipelineName = $pipeline->pipeline_name;
            $search = request('search');

            if ($status === 'deal-done') {
                // Get leads from `deal_done` table
                $leads = DB::table('deal_done')
                    ->where([
                        ['deal_done.iStatus', '=', 1],
                        ['deal_done.isDelete', '=', 0],
                        ['deal_done.iCustomerId', '=', $companyId],
                    ])
                    ->whereIn('deal_done.status', function ($query) use ($companyId, $pipelineName) {
                        $query->select('pipeline_id')
                            ->from('lead_pipeline_master')
                            ->where('company_id', $companyId)
                            ->where('pipeline_name', 'like', $pipelineName);
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
                    ->paginate(config('app.per_page'));
            } elseif ($status === 'deal-cancel') {
                // Get leads from `deal_cancel` table
                $leads = DB::table('deal_cancel')
                    ->where([
                        ['deal_cancel.iStatus', '=', 1],
                        ['deal_cancel.isDelete', '=', 0],
                        ['deal_cancel.iCustomerId', '=', $companyId],
                    ])
                    ->whereIn('deal_cancel.status', function ($query) use ($companyId, $pipelineName) {
                        $query->select('pipeline_id')
                            ->from('lead_pipeline_master')
                            ->where('company_id', $companyId)
                            ->where('pipeline_name', 'like', $pipelineName);
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
                    ->paginate(config('app.per_page'));
            } else {
                // Get leads from `lead_master` table
                $leads = LeadMaster::where([
                    'lead_master.iStatus' => 1,
                    'lead_master.isDelete' => 0,
                    'lead_master.iCustomerId' => $companyId
                ])
                    ->whereIn('lead_master.status', function ($query) use ($companyId, $pipelineName) {
                        $query->select('pipeline_id')
                            ->from('lead_pipeline_master')
                            ->where('company_id', $companyId)
                            ->where('pipeline_name', 'like', $pipelineName);
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
                    ->paginate(config('app.per_page'));
            }

            return view('company_client.follow_up.new_leads', compact('leads', 'status'));
        } catch (\Exception $e) {
            Log::error('Error in new_lead function', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'status' => $status,
                'user_id' => Auth::id(),
                'company_id' => Auth::user()->company_id ?? null
            ]);

            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function followup_detail($status, $id)
    {
        $user = Auth::user();
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

        $leadPipeline = LeadPipeline::where(['company_id' => $user->company_id])->get();
        $leadCancelList = LeadCancelReason::where(['company_id' => $user->company_id])->get();

        $lead_history = LeadHistory::where([
            'lead_history.iCustomerId' => Auth::user()->company_id,
            'lead_history.iLeadId' => $id
        ])
            ->select(
                'lead_history.*',
                'lead_pipeline_master.pipeline_name',
                'lead_cancel_reason.reason',
                // 'service_master.service_name',
                // 'lead_source_master.lead_source_name'
            )
            ->orderBY('iLeadHistoryId', 'desc')
            // ->join('lead_master', 'lead_history.iLeadId', '=', 'lead_master.lead_id')
            // ->join('service_master', 'lead_master.product_service_id', '=', 'service_master.service_id')
            ->join('lead_pipeline_master', 'lead_history.status', '=', 'lead_pipeline_master.pipeline_id')
            ->leftjoin('lead_cancel_reason', 'lead_history.cancel_reason_id', '=', 'lead_cancel_reason.lead_cancel_reason_id')
            // ->join('lead_source_master', 'lead_master.LeadSourceId', '=', 'lead_source_master.lead_source_id')
            ->paginate(config('app.per_page'));
            
        $leadUdfData = LeadUdfData::select(
            'lead_udf_data.*',
            'udf_masters.label'
        )
            ->leftjoin('udf_masters', 'lead_udf_data.udf_id', '=', 'udf_masters.id')
            ->where(['lead_udf_data.lead_id' => $id, 'lead_udf_data.isDelete' => 0])
            ->get();

        return view('company_client.follow_up.followup-detail', compact('lead', 'leadPipeline', 'leadCancelList', 'lead_history', 'id', 'status','leadUdfData'));
    }

    public function followup_update(Request $request)
    {
        try {

            $request->validate([
                'status' => 'required',
                'cancel_reason_id' => 'nullable|exists:lead_cancel_reason,lead_cancel_reason_id',
                'followup_datetime' => 'nullable',
                'amount' => 'nullable|numeric',
                'comment' => 'required',
            ]);

            $emp = Auth::guard('web_employees')->user();
            $lead = LeadMaster::findOrFail($request->lead_id);
            $user = Auth::user();

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

            return redirect()->route('clients.new_lead', $request->lead_status)->with('success', 'Follow-up updated successfully.');
        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
