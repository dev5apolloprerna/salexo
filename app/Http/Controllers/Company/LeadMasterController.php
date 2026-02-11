<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\DealCancel;
use App\Models\DealDone;
use App\Models\Employee;
use App\Models\LeadCancelReason;
use App\Models\LeadHistory;
use App\Repositories\Lead\LeadRepositoryInterface;
use App\Models\State;
use App\Models\LeadSource;
use App\Models\Service;
use App\Models\LeadMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\LeadPipeline;
use App\Models\LeadUdfData;
use App\Models\UdfMaster;
use Illuminate\Support\Facades\Log;

class LeadMasterController extends Controller
{

    protected $leadRepo;
    public function __construct(LeadRepositoryInterface $leadRepo)
    {
        $this->leadRepo = $leadRepo;
    }

    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $pipeline_id = $request->input('pipeline_id');
            $service_id = $request->input('service_id');
            $emp_id = $request->input('emp_id');
            $user = Auth::user();

            $leadPipeline = LeadPipeline::where(['company_id' => $user->company_id])->get();
            $services = Service::where(['company_id' => $user->company_id])->get();
            $employees = Employee::orderBy('emp_name', 'asc')->where(['isDelete' => 0, 'company_id' => Auth::user()->company_id])->get();

            $query = LeadMaster::select(
                'lead_master.*',
                'lead_pipeline_master.pipeline_name',
                'lead_pipeline_master.slugname as pipelineSlug',
                'service_master.service_name'
            )
                ->orderBy('lead_id', 'desc')
                ->leftjoin('lead_pipeline_master', 'lead_master.status', '=', 'lead_pipeline_master.pipeline_id')
                ->leftjoin('service_master', 'lead_master.product_service_id', '=', 'service_master.service_id')
                ->where([
                    'lead_master.isDelete' => 0,
                    'lead_master.iCustomerId' => Auth::user()->company_id
                ]);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%");
                });
            }

            if ($emp_id) {
                $query->where('iemployeeId', '=', $emp_id);
            }

            if ($pipeline_id) {
                $query->where('status', '=', $pipeline_id);
            }

            if ($service_id) {
                $query->where('product_service_id', '=', $service_id);
            }

            $leads = $query->paginate(config('app.per_page'));
            // dd($leads);

            return view('company_client.leads.index', compact('leads', 'search', 'leadPipeline', 'pipeline_id', 'services', 'service_id', 'employees', 'emp_id'));
        } catch (\Exception $e) {
            Log::error('Error in LeadMasterController@index: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred while fetching leads.');
        }
    }

    public function leads_done(Request $request)
    {
        try {
            $search = $request->input('search');
            $pipeline_id = $request->input('pipeline_id');
            $service_id = $request->input('service_id');
            $emp_id = $request->input('emp_id');
            $user = Auth::user();

            $leadPipeline = LeadPipeline::where(['company_id' => $user->company_id])->get();
            $services = Service::where(['company_id' => $user->company_id])->get();
            $employees = Employee::orderBy('emp_name', 'asc')->where(['isDelete' => 0, 'company_id' => Auth::user()->company_id])->get();

            $query = DealDone::select(
                'deal_done.*',
                'lead_pipeline_master.pipeline_name',
                'lead_pipeline_master.slugname as pipelineSlug',
                'service_master.service_name'
            )
                ->orderBy('lead_id', 'desc')
                ->leftjoin('lead_pipeline_master', 'deal_done.status', '=', 'lead_pipeline_master.pipeline_id')
                ->leftjoin('service_master', 'deal_done.product_service_id', '=', 'service_master.service_id')
                ->where([
                    'deal_done.isDelete' => 0,
                    'deal_done.iCustomerId' => Auth::user()->company_id
                ]);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%");
                });
            }

            if ($pipeline_id) {
                $query->where('status', '=', $pipeline_id);
            }

            if ($service_id) {
                $query->where('product_service_id', '=', $service_id);
            }

            if ($emp_id) {
                $query->where('iemployeeId', '=', $emp_id);
            }

            $leads = $query->paginate(config('app.per_page'));

            return view('company_client.leads.deal_done', compact('leads', 'search', 'leadPipeline', 'pipeline_id', 'services', 'service_id', 'employees', 'emp_id'));
        } catch (\Exception $e) {
            Log::error('Error in LeadMasterController@index: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred while fetching leads.');
        }
    }

    public function leads_cancel(Request $request)
    {
        try {
            $search = $request->input('search');
            $pipeline_id = $request->input('pipeline_id');
            $service_id = $request->input('service_id');
            $emp_id = $request->input('emp_id');
            $user = Auth::user();

            $leadPipeline = LeadPipeline::where(['company_id' => $user->company_id])->get();
            $services = Service::where(['company_id' => $user->company_id])->get();
            $employees = Employee::orderBy('emp_name', 'asc')->where(['isDelete' => 0, 'company_id' => Auth::user()->company_id])->get();

            $query = DealCancel::orderBy('lead_id', 'desc')->select(
                'deal_cancel.*',
                'lead_pipeline_master.pipeline_name',
                'lead_pipeline_master.slugname as pipelineSlug',
                'service_master.service_name',
                'lead_cancel_reason.reason as cancel_reason_name'
            )
                ->where([
                    'deal_cancel.isDelete' => 0,
                    'deal_cancel.iCustomerId' => Auth::user()->company_id
                ])
                ->leftjoin('lead_pipeline_master', 'deal_cancel.status', '=', 'lead_pipeline_master.pipeline_id')
                ->leftjoin('service_master', 'deal_cancel.product_service_id', '=', 'service_master.service_id')
                ->leftjoin('lead_cancel_reason', 'deal_cancel.cancel_reason_id', '=', 'lead_cancel_reason.lead_cancel_reason_id');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%");
                });
            }

            if ($pipeline_id) {
                $query->where('status', '=', $pipeline_id);
            }

            if ($service_id) {
                $query->where('product_service_id', '=', $service_id);
            }

            if ($emp_id) {
                $query->where('iemployeeId', '=', $emp_id);
            }

            $leads = $query->paginate(config('app.per_page'));

            return view('company_client.leads.deal_cancel', compact('leads', 'search', 'leadPipeline', 'pipeline_id', 'services', 'service_id', 'employees', 'emp_id'));
        } catch (\Exception $e) {
            Log::error('Error in LeadMasterController@index: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred while fetching leads.');
        }
    }

    public function create()
    {
        try {

            $employees = Employee::where(['company_id' => Auth::user()->company_id])->get();
            $leadPipeline = LeadPipeline::where(['company_id' => Auth::user()->company_id])->get();
            $leadCancelList = LeadCancelReason::where(['company_id' => Auth::user()->company_id])->get();
            $leadSources = LeadSource::where(['company_id' => Auth::user()->company_id])->pluck('lead_source_name', 'lead_source_id');
            $service = Service::where(['company_id' => Auth::user()->company_id])->pluck('service_name', 'service_id');

            $udf = UdfMaster::orderBy('id', 'asc')
                ->where(['company_id' => Auth::user()->company_id])
                ->get();

            return view('company_client.leads.create', compact('leadSources', 'service', 'leadPipeline', 'leadCancelList', 'employees', 'udf'));
        } catch (\Exception $e) {
            Log::error('Error in LeadMasterController@create: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred while opening the create lead form.');
        }
    }

    public function store(Request $request)
    {

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'mobile' => 'required',
            'initially_contacted' => 'required',
            // 'LeadSourceId' => 'required|exists:lead_source_master,lead_source_id',
            'product_service_id'    => ['required'], // numeric id OR the literal "other"
            'product_service_other' => ['nullable', 'string', 'max:255', 'required_if:product_service_id,other'],

            'LeadSourceId'          => ['required'],
            'LeadSource_other'      => ['nullable', 'string', 'max:255', 'required_if:LeadSourceId,other'],

        ]);


        try {

            $new_lead = LeadPipeline::where([
                'company_id' => Auth::user()->company_id,
                'pipeline_name' => "New Lead"
            ])->first();

            $lead_done = LeadPipeline::where([
                'company_id' => Auth::user()->company_id,
                'pipeline_name' => "Deal Done"
            ])->first();


            $data = $request->all();

            if ($request->input('product_service_id') === 'other') {
                $data['product_service_id']    = 0;
            } else {
                $data['product_service_other'] = null;
            }

            if ($request->input('LeadSourceId') === 'other') {
                $data['LeadSourceId']     = 0;
            } else {
                $data['LeadSource_other'] = null;
            }


            $lead = LeadMaster::create([
                "iCustomerId"          => $data['iCustomerId'] ?? 0,
                "iemployeeId"          => $data['iemployeeId'] ?? 0,
                "company_name"         => $data['company_name'] ?? null,
                "GST_No"               => $data['GST_No'] ?? 0,
                "customer_name"        => $data['customer_name'],
                "email"                => $data['email'] ?? null,
                "mobile"               => $data['mobile'],
                "address"              => $data['address'] ?? null,
                "alternative_no"       => $data['alternative_no'] ?? null,
                "remarks"              => $data['remarks'] ?? null,

                "product_service_id"   => $data['product_service_id'],     // NULL or int
                "product_service_other" => $data['product_service_other'],  // NULL or text

                "LeadSourceId"         => $data['LeadSourceId'],           // NULL or int
                "LeadSource_other"     => $data['LeadSource_other'],       // NULL or text

                "lead_history_id"      => $data['lead_history_id'] ?? 0,
                "comments"             => $data['comment'] ?? null,
                "followup_by"          => 0,
                "next_followup_date"   => $data['followup_datetime'] ?? null,
                "status"               => $data['status'] ?? 0,
                "cancel_reason_id"     => $data['cancel_reason_id'] ?? 0,
                "amount"               => $data['amount'] ?? null,
                "employee_id"          => $data['employee_id'] ?? 0,
                "initially_contacted"  => $data['initially_contacted'],
                "created_at"           => now(),
                'iEnterBy'             => Auth::user()->emp_id,
            ]);

            if ($request->has('udf') && is_array($request->udf)) {
                foreach ($request->udf as $udfId => $value) {
                    LeadUdfData::create([
                        'lead_id'  => $lead->lead_id,
                        'udf_id'   => $udfId,
                        'value'    => $value,
                        'created_at' => now(),
                        'strIP'    => $request->ip(),
                    ]);
                }
            }

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
                    'status' => $new_lead->pipeline_id
                ]);
            }

            if ($lead_done && $lead_done->pipeline_id == $request->status) {
                $lead->update([
                    'deal_done_at' => now()
                ]);
            }

            return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
        } catch (\Exception $e) {
            Log::error('Error in LeadMasterController@store: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred while creating the lead.');
        }
    }

    public function edit($id)
    {
        try {

            $employees = Employee::where(['company_id' => Auth::user()->company_id])->get();
            $leadPipeline = LeadPipeline::where(['company_id' => Auth::user()->company_id])->get();
            $leadCancelList = LeadCancelReason::where(['company_id' => Auth::user()->company_id])->get();
            $lead = $this->leadRepo->find($id);
            $leadSources = LeadSource::where(['company_id' => Auth::user()->company_id])->pluck('lead_source_name', 'lead_source_id');
            $service = Service::where(['company_id' => Auth::user()->company_id])->pluck('service_name', 'service_id');

            $udf = UdfMaster::orderBy('id', 'asc')
                ->where(['company_id' => Auth::user()->company_id])
                ->get();

            $leadUdfData = LeadUdfData::where('lead_id', $id)
                ->where('isDelete', 0)
                ->pluck('value', 'udf_id');

            return view('company_client.leads.edit', compact('lead', 'leadSources', 'service', 'leadPipeline', 'leadCancelList', 'employees', 'udf', 'leadUdfData'));
        } catch (\Exception $e) {
            Log::error('Error in LeadMasterController@edit: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred while opening the edit lead form.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'GST_No' => 'nullable',
            'customer_name' => 'required|string|max:255',
            'email' => 'nullable',
            'mobile' => 'required',
            'alternative_no' => 'nullable',
            'remarks' => 'nullable',

            'product_service_id'    => ['required'],
            'product_service_other' => ['nullable', 'string', 'max:255', 'required_if:product_service_id,other'],

            'LeadSourceId'          => ['required'],
            'LeadSource_other'      => ['nullable', 'string', 'max:255', 'required_if:LeadSourceId,other'],

        ]);

        try {

            $lead = LeadMaster::where(['lead_id' => $id])->first();

            if (!$lead) {
                return redirect()->back()->with('error', 'Lead not found.');
            }

            $data = $request->all();

            if ($request->input('product_service_id') === 'other') {
                $data['product_service_id']    = 0;
            } else {
                $data['product_service_other'] = null;
            }

            if ($request->input('LeadSourceId') === 'other') {
                $data['LeadSourceId']     = 0;
            } else {
                $data['LeadSource_other'] = null;
            }

            LeadMaster::where(['lead_id' => $id])->update([
                "iCustomerId"          => $data['iCustomerId'] ?? 0,
                "iemployeeId"          => $data['iemployeeId'] ?? 0,
                "company_name"         => $data['company_name'] ?? null,
                "GST_No"               => $data['GST_No'] ?? null,
                "customer_name"        => $data['customer_name'],
                "email"                => $data['email'] ?? null,
                "mobile"               => $data['mobile'],
                "alternative_no"       => $data['alternative_no'] ?? null,
                "address"              => $data['address'] ?? null,
                "remarks"              => $data['remarks'] ?? null,

                "product_service_id"   => $data['product_service_id'],     // NULL or int
                "product_service_other" => $data['product_service_other'],  // NULL or text

                "LeadSourceId"         => $data['LeadSourceId'],           // NULL or int
                "LeadSource_other"     => $data['LeadSource_other'],       // NULL or text

                "comments"             => $data['comment'] ?? null,
                "employee_id"          => $data['employee_id'] ?? 0,
                "updated_at"           => now(),
            ]);

            // ✅ Handle UDF data
            if ($request->has('udf')) {
                foreach ($request->udf as $udfId => $value) {
                    LeadUdfData::updateOrCreate(
                        [
                            'lead_id' => $id,
                            'udf_id'  => $udfId,
                        ],
                        [
                            'value'       => $value ?? '',
                            'updated_at'  => now(),
                            'strIP'       => $request->ip(),
                        ]
                    );
                }
            }

            return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error in LeadMasterController@update: ' . $e->getMessage(), [
                'lead_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'An error occurred while updating the lead.');
        }
    }

    public function destroy(Request $request)
    {
        try {
            $lead = LeadMaster::where('lead_id', $request->lead_id)->first();

            if ($lead) {
                $lead->delete();
                return redirect()->back()->with('success', 'Lead deleted successfully.');
            } else {
                return redirect()->back()->with('error', 'Lead not found.');
            }
        } catch (\Exception $e) {
            Log::error('Error deleting lead: ' . $e->getMessage(), [
                'lead_id' => $request->lead_id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'An error occurred while deleting the lead.');
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

            $leadUdfData = LeadUdfData::select(
                'lead_udf_data.*',
                'udf_masters.label'
            )
                ->leftjoin('udf_masters', 'lead_udf_data.udf_id', '=', 'udf_masters.id')
                ->where(['lead_udf_data.lead_id' => $lead_id, 'lead_udf_data.isDelete' => 0])
                ->get();

            return view('company_client.leads.lead_history', compact('lead_history', 'status', 'lead', 'leadUdfData'));
        } catch (\Exception $e) {
            Log::error('Error in LeadMasterController@index: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred while fetching leads.');
        }
    }

    public function export_to_excel(Request $request)
    {
        // dd($request);
        $LeadType   = $request->lead_type;
        $Search     = $request->search;
        $EmpId      = $request->emp_id;
        $PipelineId = $request->pipeline_id;
        $ServiceId  = $request->service_id;

        // Pick the base model/table based on lead type
        if ($LeadType === "active_deal") {

            $query = LeadMaster::select(
                'lead_master.*',
                'lead_pipeline_master.pipeline_name',
                'lead_pipeline_master.slugname as pipelineSlug',
                'service_master.service_name'
            )
                ->leftJoin('lead_pipeline_master', 'lead_master.status', '=', 'lead_pipeline_master.pipeline_id')
                ->leftJoin('service_master', 'lead_master.product_service_id', '=', 'service_master.service_id')
                ->where([
                    'lead_master.isDelete'     => 0,
                    'lead_master.iCustomerId'  => Auth::user()->company_id,
                ])
                ->orderBy('lead_master.lead_id', 'desc');
        } elseif ($LeadType === "deal_done") {

            $query = DealDone::select(
                'deal_done.*',
                'lead_pipeline_master.pipeline_name',
                'lead_pipeline_master.slugname as pipelineSlug',
                'service_master.service_name'
            )
                ->leftJoin('lead_pipeline_master', 'deal_done.status', '=', 'lead_pipeline_master.pipeline_id')
                ->leftJoin('service_master', 'deal_done.product_service_id', '=', 'service_master.service_id')
                ->where([
                    'deal_done.isDelete'     => 0,
                    'deal_done.iCustomerId'  => Auth::user()->company_id,
                ])
                ->orderBy('deal_done.lead_id', 'desc');
        } else {

            $query = DealCancel::select(
                'deal_cancel.*',
                'lead_pipeline_master.pipeline_name',
                'lead_pipeline_master.slugname as pipelineSlug',
                'service_master.service_name',
                'lead_cancel_reason.reason as cancel_reason_name'
            )
                ->leftJoin('lead_pipeline_master', 'deal_cancel.status', '=', 'lead_pipeline_master.pipeline_id')
                ->leftJoin('service_master', 'deal_cancel.product_service_id', '=', 'service_master.service_id')
                ->leftjoin('lead_cancel_reason', 'deal_cancel.cancel_reason_id', '=', 'lead_cancel_reason.lead_cancel_reason_id')
                ->where([
                    'deal_cancel.isDelete'     => 0,
                    'deal_cancel.iCustomerId'  => Auth::user()->company_id,
                ])
                ->orderBy('deal_cancel.lead_id', 'desc');
            // ->get();
        }

        // dd($ServiceId);
        // ðŸ”Ž Filters
        if ($Search) {
            $query->where(function ($q) use ($Search) {
                $q->where('company_name', 'like', "%{$Search}%")
                    ->orWhere('customer_name', 'like', "%{$Search}%");
            });
        }

        if ($EmpId) {
            $query->where('iemployeeId', $EmpId);
        }

        if ($PipelineId) {
            $query->where('status', $PipelineId);
        }

        if ($ServiceId) {
            $query->where('product_service_id', $ServiceId);
        }
        // dd($query)    ;

        $leads = $query->get();
        // dd($leads)    ;

        return view('company_client.leads.excel', compact('leads', 'LeadType'));
    }
}
