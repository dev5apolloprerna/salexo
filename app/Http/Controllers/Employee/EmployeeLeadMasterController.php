<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\DealCancel;
use App\Models\DealDone;
use App\Models\Employee;
use App\Models\LeadCancelReason;
use App\Models\LeadHistory;
use App\Models\LeadSource;
use App\Models\Service;
use App\Models\LeadMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\LeadPipeline;
use Illuminate\Support\Facades\Log;

class EmployeeLeadMasterController extends Controller
{
    public function lead_list(Request $request)
    {
        try {
            $search = $request->input('search');

            // $query = LeadMaster::where([
            //     'isDelete' => 0,
            //     'iCustomerId' => Auth::user()->company_id,
            //     'iEnterBy' => Auth::user()->emp_id,
            // ]);
            $query = LeadMaster::select(
                'lead_master.*',
                'lead_pipeline_master.pipeline_name',
                'lead_pipeline_master.slugname as pipelineSlug',
                'service_master.service_name'
            )
                ->orderBy('lead_id','desc')
                ->leftjoin('lead_pipeline_master', 'lead_master.status', '=', 'lead_pipeline_master.pipeline_id')
                ->leftjoin('service_master', 'lead_master.product_service_id', '=', 'service_master.service_id')
                ->where([
                    'lead_master.isDelete' => 0,
                    'lead_master.iCustomerId' => Auth::user()->company_id,
                    'iEnterBy' => Auth::user()->emp_id,
                ]);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%");
                });
            }

            $leads = $query->paginate(config('app.per_page'));

            return view('employee.leads.index', compact('leads', 'search'));
        } catch (\Exception $e) {
            Log::error('Lead list fetch failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function leads_done(Request $request)
    {
        try {
            $search = $request->input('search');
            $query = DealDone::where([
                'isDelete' => 0,
                'iCustomerId' => Auth::user()->company_id,
                'iEnterBy' => Auth::user()->emp_id,
            ]);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%");
                });
            }

            $leads = $query->paginate(config('app.per_page'));

            return view('employee.leads.deal_done', compact('leads', 'search'));
        } catch (\Exception $e) {
            Log::error('Error in LeadMasterController@index: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred while fetching leads.');
        }
    }

    public function leads_cancel(Request $request)
    {
        try {
            $search = $request->input('search');
            $query = DealCancel::where([
                'isDelete' => 0,
                'iCustomerId' => Auth::user()->company_id,
                'iEnterBy' => Auth::user()->emp_id,
            ]);
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%");
                });
            }

            $leads = $query->paginate(config('app.per_page'));

            return view('employee.leads.deal_cancel', compact('leads', 'search'));
        } catch (\Exception $e) {
            Log::error('Error in LeadMasterController@index: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred while fetching leads.');
        }
    }

    public function lead_add()
    {
        try {

            $leadPipeline = LeadPipeline::where(['company_id' => Auth::user()->company_id])->get();
            $leadCancelList = LeadCancelReason::where(['company_id' => Auth::user()->company_id])->get();
            $leadSources = LeadSource::where(['company_id' => Auth::user()->company_id])->pluck('lead_source_name', 'lead_source_id');
            $service = Service::where(['company_id' => Auth::user()->company_id])->pluck('service_name', 'service_id');

            return view('employee.leads.create', compact('leadSources', 'service', 'leadPipeline', 'leadCancelList'));
        } catch (\Exception $e) {
            Log::error('Lead add form load failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function lead_create(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'mobile' => 'required',
            'LeadSourceId' => 'required|exists:lead_source_master,lead_source_id',
        ]);

        try {

            $leadPipeline = LeadPipeline::where([
                'company_id' => Auth::user()->company_id,
                'pipeline_name' => "New Lead"
            ])->first();

            $lead = LeadMaster::create([
                "iCustomerId" => $request->iCustomerId ?? 0,
                "iemployeeId" => $request->iemployeeId ?? 0,
                "company_name" => $request->company_name,
                "GST_No" => $request->GST_No ?? 0,
                "customer_name" => $request->customer_name,
                "email" => $request->email,
                "mobile" => $request->mobile,
                "address" => $request->address,
                "alternative_no" => $request->alternative_no,
                "remarks" => $request->remarks,
                "product_service_id" => $request->product_service_id ?? 0,
                "LeadSourceId" => $request->LeadSourceId ?? 0,
                "lead_history_id" => $request->lead_history_id ?? 0,
                "comments" => $request->comment,
                "followup_by" => 0,
                "next_followup_date" => $request->followup_datetime,
                "status" => $request->status ?? 0,
                "cancel_reason_id" => $request->cancel_reason_id ?? 0,
                "amount" => $request->amount,
                "employee_id" => Auth::user()->emp_id ?? 0,
                "initially_contacted" => $request->initially_contacted,
                "created_at" => now(),
                'iEnterBy' => Auth::user()->emp_id,
            ]);

            if ($request->initially_contacted == "Yes") {
                $leadHistoryData = [
                    'iLeadId' => $lead->lead_id ?? 0,
                    'iCustomerId' => $request->iCustomerId ?? 0,
                    'Comments' => $request->comment,
                    'followup_by' => 0,
                    'next_followup_date' => $request->followup_datetime,
                    'status' => $request->status ?? 0,
                    'cancel_reason_id' => $request->cancel_reason_id ?? 0,
                    'amount' => $request->amount ?? 0,
                    'created_at' => now(),
                    'iEnterBy' => Auth::user()->emp_id,
                ];
                $leadHistory = LeadHistory::create($leadHistoryData);

                $lead->update([
                    'lead_history_id' => $leadHistory->id
                ]);
            } else {

                $lead->update([
                    'status' => $leadPipeline->pipeline_id
                ]);
            }

            return redirect()->route('employee.leads.index')->with('success', 'Lead created successfully.');
        } catch (\Exception $e) {
            Log::error('Lead creation failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function lead_edit($id)
    {
        try {

            $leadPipeline = LeadPipeline::where(['company_id' => Auth::user()->company_id])->get();
            $leadCancelList = LeadCancelReason::where(['company_id' => Auth::user()->company_id])->get();
            $lead = LeadMaster::where(['lead_id' => $id])->first();

            $leadSources = LeadSource::where(['company_id' => Auth::user()->company_id])->pluck('lead_source_name', 'lead_source_id');
            $service = Service::where(['company_id' => Auth::user()->company_id])->pluck('service_name', 'service_id');

            return view('employee.leads.edit', compact('lead',  'leadSources', 'service', 'leadPipeline', 'leadCancelList'));
        } catch (\Exception $e) {
            Log::error('Lead edit load failed', ['lead_id' => $id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function lead_update(Request $request, $id)
    {
        $request->validate([
            'GST_No' => 'nullable',
            'customer_name' => 'required|string|max:255',
            'email' => 'nullable',
            'mobile' => 'required',
            'alternative_no' => 'nullable',
            'remarks' => 'nullable',
            'product_service_id' => 'required',
            'LeadSourceId' => 'required|exists:lead_source_master,lead_source_id',
        ]);

        try {
            $lead = LeadMaster::where(['lead_id' => $id])->first();

            if (!$lead) {
                return redirect()->back()->with('error', 'Lead not found.');
            }

            LeadMaster::where(['lead_id' => $id])->update([
                "iCustomerId" => $request->iCustomerId ?? 0,
                "iemployeeId" => $request->iemployeeId ?? 0,
                "company_name" => $request->company_name,
                "GST_No" => $request->GST_No,
                "customer_name" => $request->customer_name,
                "email" => $request->email,
                "mobile" => $request->mobile,
                "alternative_no" => $request->alternative_no,
                "address" => $request->address,
                "remarks" => $request->remarks,
                "product_service_id" => $request->product_service_id ?? 0,
                "LeadSourceId" => $request->LeadSourceId ?? 0,
                "lead_history_id" => $request->lead_history_id ?? 0,
                "comments" => $request->comment,
                "followup_by" => 0,
                "next_followup_date" => $request->followup_datetime,
                "status" => $request->status ?? 0,
                "cancel_reason_id" => $request->cancel_reason_id ?? 0,
                "amount" => $request->amount,
                "employee_id" => Auth::user()->emp_id ?? 0,
                "initially_contacted" => $request->initially_contacted,
                "updated_at" => now()
            ]);

            return redirect()->route('employee.leads.index')->with('success', 'Lead updated successfully.');
        } catch (\Exception $e) {
            Log::error('Lead update failed', ['lead_id' => $id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred while updating the lead: ' . $e->getMessage());
        }
    }

    public function lead_delete(Request $request)
    {
        try {
            $lead = LeadMaster::where([
                'lead_id' => $request->lead_id,
                'iCustomerId' => Auth::user()->company_id,
                'iEnterBy' => Auth::user()->emp_id,
                'isDelete' => 0
            ])->first();

            if (!$lead) {
                return redirect()->back()->with('error', 'Lead not found or already deleted.');
            }

            $lead->delete();

            LeadHistory::where(['iLeadId' => $request->lead_id])->delete();

            return redirect()->route('employee.leads.index')->with('success', 'Lead deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Lead deletion failed', [
                'lead_id' => $request->lead_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'An error occurred while deleting the lead: ' . $e->getMessage());
        }
    }
    
    public function lead_history(Request $request, $status, $lead_id)
    {
        try {
            $lead = LeadMaster::select(
                'lead_master.*',
                'lead_source_master.lead_source_name',
                'service_master.service_name',
                'lead_pipeline_master.pipeline_name',
            )
                ->where('lead_master.lead_id', $lead_id)
                ->leftjoin('lead_source_master', 'lead_master.LeadSourceId', '=', 'lead_source_master.lead_source_id')
                ->leftjoin('service_master', 'lead_master.product_service_id', '=', 'service_master.service_id')
                ->leftjoin('lead_pipeline_master', 'lead_master.status', '=', 'lead_pipeline_master.pipeline_id')
                ->first();
            
            $lead_history = LeadHistory::where([
                'lead_history.iCustomerId' => Auth::user()->company_id,
                'lead_history.iLeadId' => $lead_id
            ])
                ->select(
                    'lead_history.*',
                    'lead_pipeline_master.pipeline_name',
                    'lead_cancel_reason.reason'
                )
                ->orderBY('iLeadHistoryId', 'desc')
                ->join('lead_pipeline_master', 'lead_history.status', '=', 'lead_pipeline_master.pipeline_id')
                ->leftjoin('lead_cancel_reason', 'lead_history.cancel_reason_id', '=', 'lead_cancel_reason.lead_cancel_reason_id')
                ->paginate(config('app.per_page'));

            return view('employee.leads.lead_history', compact('lead_history', 'status','lead'));
        } catch (\Exception $e) {
            Log::error('Error in LeadMasterController@index: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred while fetching leads.');
        }
    }
}
