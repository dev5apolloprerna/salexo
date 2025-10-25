<?php

namespace App\Http\Controllers\Api;

use App\Exports\ReportExport;
use App\Exports\ROIReportExport;
use App\Http\Controllers\Controller;
use App\Models\DealCancel;
use App\Models\DealDone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\LeadMaster;
use App\Models\LeadPipeline;
use App\Models\LeadSource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function roi_report(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            
            $fromDateFormatted = $fromDate ? Carbon::createFromFormat('d-m-Y', $fromDate)->startOfDay() : null;
            $toDateFormatted = $toDate ? Carbon::createFromFormat('d-m-Y', $toDate)->endOfDay() : null;

            $convertedPipeline = LeadPipeline::where('company_id', $employee->company_id)
                ->where('slugname', 'deal-done')
                ->value('pipeline_id');

            $leadSources = LeadSource::where('company_id', $employee->company_id)->get();

            $reportData = [];

            foreach ($leadSources as $source) {
                // ---------------------------
                // Leads from lead_master
                // ---------------------------
                $leadMasterQuery = DB::table('lead_master')
                    ->where('isDelete', 0)
                    ->where('iCustomerId', $employee->company_id)
                    ->where('LeadSourceId', $source->lead_source_id);

                if ($fromDateFormatted) {
                    $leadMasterQuery->where('created_at', '>=', $fromDateFormatted);
                }
                if ($toDateFormatted) {
                    $leadMasterQuery->where('created_at', '<=', $toDateFormatted);
                }

                $leadMasterCount = $leadMasterQuery->count();

                // ---------------------------
                // Leads from deal_done
                // ---------------------------
                $dealDoneQuery = DB::table('deal_done')
                    ->where('isDelete', 0)
                    ->where('iCustomerId', $employee->company_id)
                    ->where('LeadSourceId', $source->lead_source_id);

                if ($fromDateFormatted) {
                    $dealDoneQuery->where('created_at', '>=', $fromDateFormatted);
                }
                if ($toDateFormatted) {
                    $dealDoneQuery->where('created_at', '<=', $toDateFormatted);
                }

                $dealDoneCount = $dealDoneQuery->count();
                $dealDoneAmount = $dealDoneQuery->sum('amount');

                $reportData[] = [
                    'lead_source_id' => $source->lead_source_id,
                    'source_name' => $source->lead_source_name,
                    'leads_found' => $leadMasterCount + $dealDoneCount,
                    'leads_converted' => $dealDoneCount,
                    'converted_amount' => round($dealDoneAmount, 2),
                ];
            }

            // Add Sr. No to each row
            $reportDataWithSrNo = [];
            foreach ($reportData as $index => $row) {
                $reportDataWithSrNo[] = array_merge(['Sr. No.' => $index + 1], $row);
            }

            $headings = ['Sr. No.', 'Lead Source', 'Leads Found', 'Leads Converted', 'Lead Conveted Amount'];

            if ($request->input('excel') == 1) {
                return $this->handleExcelExport($reportDataWithSrNo, 'roi', $headings);
            }

            return response()->json([
                'success' => true,
                'data' => $reportData,
            ]);
        } catch (\Exception $e) {
            Log::error('API ROI Report Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Internal server error.'], 500);
        }
    }

    public function emp_performance(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();

            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            
            $fromDateFormatted = $fromDate ? Carbon::createFromFormat('d-m-Y', $fromDate)->startOfDay() : null;
            $toDateFormatted = $toDate ? Carbon::createFromFormat('d-m-Y', $toDate)->endOfDay() : null;

            
            $search = $request->input('emp_name');

            $convertedPipelineId = LeadPipeline::where('slugname', 'deal-done')
                ->where('company_id', $employee->company_id)
                ->value('pipeline_id');

            $employees = Employee::where('company_id', $employee->company_id)
                ->when($employee->isCompanyAdmin == 0, function ($query) use ($employee) {
                    return $query->where('emp_id', $employee->emp_id);
                })
                ->when($search, fn($query) => $query->where('emp_name', 'like', "%{$search}%"))
                ->get();

            $result = [];

            foreach ($employees as $emp) {
                $generatedQuery = LeadMaster::where('iCustomerId', $employee->company_id)
                    ->where('iEnterBy', $emp->emp_id)
                    ->where('isDelete', 0);
                $generatedQuery1 = DealDone::where('iCustomerId', $employee->company_id)
                    ->where('iEnterBy', $emp->emp_id)
                    ->where('isDelete', 0);
                $generatedQuery2 = DealCancel::where('iCustomerId', $employee->company_id)
                    ->where('iEnterBy', $emp->emp_id)
                    ->where('isDelete', 0);

                // if ($fromDate) $generatedQuery->whereDate('created_at', '>=', $fromDate);
                // if ($toDate) $generatedQuery->whereDate('created_at', '<=', $toDate);
                
                if ($fromDateFormatted) {
                    $generatedQuery->where('created_at', '>=', $fromDateFormatted);
                    $generatedQuery1->where('created_at', '>=', $fromDateFormatted);
                    $generatedQuery2->where('created_at', '>=', $fromDateFormatted);
                }
    
                if ($toDateFormatted) {
                    $generatedQuery->where('created_at', '<=', $toDateFormatted);
                    $generatedQuery1->where('created_at', '<=', $toDateFormatted);
                    $generatedQuery2->where('created_at', '<=', $toDateFormatted);
                }
                
                $leadsGenerated = $generatedQuery->count() + $generatedQuery1->count() + $generatedQuery2->count();

                $assignedQuery = LeadMaster::where('iCustomerId', $employee->company_id)
                    ->where('iemployeeId', $emp->emp_id)
                    ->where('isDelete', 0);
                $assignedQuery1 = DealDone::where('iCustomerId', $employee->company_id)
                    ->where('iemployeeId', $emp->emp_id)
                    ->where('isDelete', 0);
                $assignedQuery2 = DealCancel::where('iCustomerId', $employee->company_id)
                    ->where('iemployeeId', $emp->emp_id)
                    ->where('isDelete', 0);

                // if ($fromDate) $assignedQuery->whereDate('created_at', '>=', $fromDate);
                // if ($toDate) $assignedQuery->whereDate('created_at', '<=', $toDate);
                
                if ($fromDateFormatted) {
                    $assignedQuery->where('created_at', '>=', $fromDateFormatted);
                    $assignedQuery1->where('created_at', '>=', $fromDateFormatted);
                    $assignedQuery2->where('created_at', '>=', $fromDateFormatted);
                }
    
                if ($toDateFormatted) {
                    $assignedQuery->where('created_at', '<=', $toDateFormatted);
                    $assignedQuery1->where('created_at', '<=', $toDateFormatted);
                    $assignedQuery2->where('created_at', '<=', $toDateFormatted);
                }
                
                $leadsAssigned = $assignedQuery->count() + $assignedQuery1->count() + $assignedQuery2->count();

                $convertedQuery = DealDone::where('iCustomerId', $employee->company_id)
                    ->where('iemployeeId', $emp->emp_id)
                    ->where('status', $convertedPipelineId)
                    ->where('isDelete', 0);
                    
                if ($fromDateFormatted) {
                    $convertedQuery->where('created_at', '>=', $fromDateFormatted);
                }
    
                if ($toDateFormatted) {
                    $convertedQuery->where('created_at', '<=', $toDateFormatted);
                }
                
                $convertedAmount = $convertedQuery->sum('amount');

                $result[] = [
                    'emp_id' => $emp->emp_id, // Include for API
                    'name' => $emp->emp_name,
                    'total' => $leadsGenerated ?? 0,
                    'leads_generated' => $leadsGenerated ?? 0,
                    'leads_assigned' => $leadsAssigned ?? 0,
                    'converted_amount' => round($convertedAmount, 2),
                ];
            }

            $reportDataWithSrNo = [];
            foreach ($result as $index => $row) {
                $filteredRow = collect($row)->except(['emp_id'])->toArray();
                $reportDataWithSrNo[] = array_merge(['Sr. No.' => $index + 1], $filteredRow);
            }

            $headings = ['Sr. No.', 'Employee Name', 'Total Received', 'Leads Generated', 'Lead Given', 'Lead Conveted Amount'];

            if ($request->input('excel') == 1) {
                return $this->handleExcelExport($reportDataWithSrNo, 'emp_performance', $headings);
            }

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('API Emp Performance Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Internal server error.'], 500);
        }
    }

    public function emp_lead_analysis(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            
            $fromDateFormatted = $fromDate ? Carbon::createFromFormat('d-m-Y', $fromDate)->startOfDay() : null;
            $toDateFormatted = $toDate ? Carbon::createFromFormat('d-m-Y', $toDate)->endOfDay() : null;

            $query = DB::table('lead_master as lm')
                ->select(
                    'lm.LeadSourceId',
                    'ls.lead_source_name',
                    'lm.status as pipeline_id',
                    'lp.pipeline_name',
                    DB::raw('COUNT(*) as lead_count'),
                    DB::raw('SUM(lm.amount) as total_amount')
                )
                ->leftJoin('lead_source_master as ls', 'ls.lead_source_id', '=', 'lm.LeadSourceId')
                ->leftJoin('lead_pipeline_master as lp', 'lp.pipeline_id', '=', 'lm.status')
                ->where('lm.iCustomerId', $employee->company_id)
                ->whereNotIn('lp.slugname', ['deal-done', 'deal-cancel'])
                ->where('lm.isDelete', 0)
                ->groupBy('lm.LeadSourceId', 'lm.status', 'ls.lead_source_name', 'lp.pipeline_name');

            if ($fromDateFormatted) {
                $query->where('lm.created_at', '>=', $fromDateFormatted);
            }

            if ($toDateFormatted) {
                $query->where('lm.created_at', '<=', $toDateFormatted);
            }

            $queryDone = DB::table('deal_done as lm')
                ->select(
                    'lm.LeadSourceId',
                    'ls.lead_source_name',
                    'lm.status as pipeline_id',
                    'lp.pipeline_name',
                    DB::raw('COUNT(*) as lead_count'),
                    DB::raw('SUM(lm.amount) as total_amount')
                )
                ->leftJoin('lead_source_master as ls', 'ls.lead_source_id', '=', 'lm.LeadSourceId')
                ->leftJoin('lead_pipeline_master as lp', 'lp.pipeline_id', '=', 'lm.status')
                ->where('lm.iCustomerId', $employee->company_id)
                ->whereIn('lp.slugname', ['deal-done'])
                ->where('lm.isDelete', 0)
                ->groupBy('lm.LeadSourceId', 'lm.status', 'ls.lead_source_name', 'lp.pipeline_name');
                
            if ($fromDateFormatted) {
                $queryDone->where('lm.created_at', '>=', $fromDateFormatted);
            }
            if ($toDateFormatted) {
                $queryDone->where('lm.created_at', '<=', $toDateFormatted);
            }

            $queryCancel = DB::table('deal_cancel as lm')
                ->select(
                    'lm.LeadSourceId',
                    'ls.lead_source_name',
                    'lm.status as pipeline_id',
                    'lp.pipeline_name',
                    DB::raw('COUNT(*) as lead_count'),
                    DB::raw('SUM(lm.amount) as total_amount')
                )
                ->leftJoin('lead_source_master as ls', 'ls.lead_source_id', '=', 'lm.LeadSourceId')
                ->leftJoin('lead_pipeline_master as lp', 'lp.pipeline_id', '=', 'lm.status')
                ->where('lm.iCustomerId', $employee->company_id)
                ->whereIn('lp.slugname', ['deal-cancel'])
                ->where('lm.isDelete', 0)
                ->groupBy('lm.LeadSourceId', 'lm.status', 'ls.lead_source_name', 'lp.pipeline_name');
                
            if ($fromDateFormatted) {
                $queryCancel->where('lm.created_at', '>=', $fromDateFormatted);
            }
            if ($toDateFormatted) {
                $queryCancel->where('lm.created_at', '<=', $toDateFormatted);
            }
            $reportData = $query->union($queryDone)->union($queryCancel)->get();


            $formatted = json_decode(json_encode($reportData), true);
            $reportDataWithSrNo = [];

            foreach ($formatted as $index => $row) {
                $reportDataWithSrNo[] = array_merge(['Sr. No.' => $index + 1], $row);
            }

            $headings = ['Sr. No.', 'Lead Received', 'Pipeline', 'Count', 'Total'];

            if ($request->input('excel') == 1) {
                return $this->handleExcelExport($reportDataWithSrNo, 'lead_analysis', $headings);
            }

            return response()->json([
                'success' => true,
                'data' => $reportData,
            ]);
        } catch (\Exception $e) {
            Log::error('API Lead Analysis Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Internal server error.'], 500);
        }
    }

    public function emp_lead_cancel_analysis(Request $request)
    {
        // try {
        $employee = Auth::guard('employee_api')->user();
        
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        
        $fromDateFormatted = $fromDate ? Carbon::createFromFormat('d-m-Y', $fromDate)->startOfDay() : null;
        $toDateFormatted = $toDate ? Carbon::createFromFormat('d-m-Y', $toDate)->endOfDay() : null;

        // Get the pipeline_id for cancelled leads
        $cancelledPipelineId = LeadPipeline::where('slugname', 'deal-cancel')
            ->where('company_id', $employee->company_id)
            ->value('pipeline_id');

        // Fetch lead counts by source where status = cancelled

        $query = DB::table('deal_cancel as lm')
            ->select(
                'lm.cancel_reason_id',
                'cr.reason',
                DB::raw('COUNT(*) as lead_count')
            )
            ->leftJoin('lead_cancel_reason as cr', 'cr.lead_cancel_reason_id', '=', 'lm.cancel_reason_id')
            ->where('lm.iCustomerId', $employee->company_id)
            ->where('lm.isDelete', 0)
            ->where('lm.status', $cancelledPipelineId);

        if ($fromDateFormatted) {
            $query->where('lm.created_at', '>=', $fromDateFormatted);
        }
        if ($toDateFormatted) {
            $query->where('lm.created_at', '<=', $toDateFormatted);
        }

        $query->groupBy('cr.reason', 'lm.cancel_reason_id');

        $reportData = $query->get();

        $formatted = json_decode(json_encode($reportData), true);
        $reportDataWithSrNo = [];

        foreach ($formatted as $index => $row) {
            $reportDataWithSrNo[] = array_merge(['Sr. No.' => $index + 1], $row);
        }

        $headings = ['Sr. No.', 'Cancel Reason', 'Lead Count'];

        if ($request->input('excel') == 1) {
            return $this->handleExcelExport($reportDataWithSrNo, 'lead_cancel_analysis', $headings);
        }

        return response()->json([
            'success' => true,
            'data' => $reportData,
        ]);
        
        // } catch (\Exception $e) {
        //     Log::error('API Lead Cancel Analysis Error: ' . $e->getMessage());
        //     return response()->json(['success' => false, 'message' => 'Internal server error.'], 500);
        // }
    }

    public function lead_found_detail(Request $request)
    {
        try {
            $request->validate([
                'source_id' => 'required'
            ]);
            $employee = Auth::guard('employee_api')->user();
            
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            
            $fromDateFormatted = $fromDate ? Carbon::createFromFormat('d-m-Y', $fromDate)->startOfDay() : null;
            $toDateFormatted = $toDate ? Carbon::createFromFormat('d-m-Y', $toDate)->endOfDay() : null;


            $companyId = $employee->company_id;
            $sourceId = $request->source_id;

            // Common SELECT fields
            $commonSelect = [
                'lead_id',
                'company_name',
                'customer_name',
                'email',
                'mobile',
                'LeadSourceId',
                'product_service_id',
                'created_at',
            ];

            // Lead Master
            $leadMaster = DB::table('lead_master')
                ->select(array_merge($commonSelect, [
                    DB::raw("'lead_master' as source")
                ]))
                ->where('isDelete', 0)
                ->where('iCustomerId', $companyId)
                ->where('LeadSourceId', $sourceId);

            if ($fromDateFormatted) {
                $leadMaster->where('created_at', '>=', $fromDateFormatted);
            }
            if ($toDateFormatted) {
                $leadMaster->where('created_at', '<=', $toDateFormatted);
            }

            // Deal Done
            $dealDone = DB::table('deal_done')
                ->select(array_merge($commonSelect, [
                    DB::raw("'deal_done' as source")
                ]))
                ->where('isDelete', 0)
                ->where('iCustomerId', $companyId)
                ->where('LeadSourceId', $sourceId);

            if ($fromDateFormatted) {
                $dealDone->where('created_at', '>=', $fromDateFormatted);
            }
            if ($toDateFormatted) {
                $dealDone->where('created_at', '<=', $toDateFormatted);
            }

            // Combine using UNION
            $combinedLeads = $leadMaster->unionAll($dealDone);

            // Join with master tables
            $leads = DB::table(DB::raw("({$combinedLeads->toSql()}) as all_leads"))
                ->mergeBindings($combinedLeads)
                ->leftJoin('lead_source_master', 'lead_source_master.lead_source_id', '=', 'all_leads.LeadSourceId')
                ->leftJoin('service_master', 'service_master.service_id', '=', 'all_leads.product_service_id')
                ->select(
                    'all_leads.lead_id',
                    'all_leads.company_name',
                    'all_leads.customer_name',
                    'all_leads.email',
                    'all_leads.mobile',
                    'lead_source_master.lead_source_name',
                    'service_master.service_name',
                    'all_leads.source',
                    'all_leads.created_at'
                )
                ->orderByDesc('all_leads.created_at')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $leads,
            ]);
        } catch (\Exception $e) {
            Log::error('API Lead Found Detail Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Internal server error.'], 500);
        }
    }

    public function lead_converted_detail(Request $request)
    {
        try {
            $request->validate([
                'source_id' => 'required'
            ]);

            $employee = Auth::guard('employee_api')->user();
            
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            
            $fromDateFormatted = $fromDate ? Carbon::createFromFormat('d-m-Y', $fromDate)->startOfDay() : null;
            $toDateFormatted = $toDate ? Carbon::createFromFormat('d-m-Y', $toDate)->endOfDay() : null;

            $companyId = $employee->company_id;
            $sourceId = $request->source_id;

            // Common fields to select
            $selectFields = [
                'lead_id',
                'company_name',
                'customer_name',
                'email',
                'mobile',
                'LeadSourceId',
                'product_service_id',
                'amount',
                'created_at',
            ];

            // Deal Done Query (Converted Leads)
            $dealDone = DB::table('deal_done')
                ->select(array_merge($selectFields, [
                    DB::raw("'deal_done' as source")
                ]))
                ->where('isDelete', 0)
                ->where('iCustomerId', $companyId)
                ->where('LeadSourceId', $sourceId);

            if ($fromDateFormatted) {
                $dealDone->where('created_at', '>=', $fromDateFormatted);
            }
            if ($toDateFormatted) {
                $dealDone->where('created_at', '<=', $toDateFormatted);
            }

            // Join with source and service tables
            $leads = DB::table(DB::raw("({$dealDone->toSql()}) as converted_leads"))
                ->mergeBindings($dealDone)
                ->leftJoin('lead_source_master', 'lead_source_master.lead_source_id', '=', 'converted_leads.LeadSourceId')
                ->leftJoin('service_master', 'service_master.service_id', '=', 'converted_leads.product_service_id')
                ->select(
                    'converted_leads.lead_id',
                    'converted_leads.company_name',
                    'converted_leads.customer_name',
                    'converted_leads.email',
                    'converted_leads.mobile',
                    'lead_source_master.lead_source_name',
                    'service_master.service_name',
                    'converted_leads.amount',
                    'converted_leads.source',
                    'converted_leads.created_at'
                )
                ->orderByDesc('converted_leads.created_at')
                ->get();
            // dd($leads);

            return response()->json([
                'success' => true,
                'data' => $leads,
            ]);
        } catch (\Exception $e) {
            Log::error('API Lead Converted Detail Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Internal server error.'], 500);
        }
    }

    public function lead_generated_detail(Request $request)
    {
        try {
            $request->validate([
                'emp_id' => 'required'
            ]);

            $employee = Auth::guard('employee_api')->user();

            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            
            $fromDateFormatted = $fromDate ? Carbon::createFromFormat('d-m-Y', $fromDate)->startOfDay() : null;
            $toDateFormatted = $toDate ? Carbon::createFromFormat('d-m-Y', $toDate)->endOfDay() : null;

            // Common SELECT fields from all tables
            $commonSelect = [
                'lead_id',
                'company_name',
                'customer_name',
                'email',
                'mobile',
                'LeadSourceId',
                'product_service_id',
                'created_at',
            ];

            // Lead Master Query
            $leadMaster = DB::table('lead_master')
                ->select(array_merge($commonSelect, [
                    DB::raw("'lead_master' as source")
                ]))
                ->where([
                    ['iCustomerId', '=', $employee->company_id],
                    ['iEnterBy', '=', $request->emp_id],
                    ['isDelete', '=', 0]
                ]);
                
            if ($fromDateFormatted) {
                $leadMaster->where('created_at', '>=', $fromDateFormatted);
            }
    
            if ($toDateFormatted) {
                $leadMaster->where('created_at', '<=', $toDateFormatted);
            }    

            // Deal Done Query
            $dealDone = DB::table('deal_done')
                ->select(array_merge($commonSelect, [
                    DB::raw("'deal_done' as source")
                ]))
                ->where([
                    ['iCustomerId', '=', $employee->company_id],
                    ['iEnterBy', '=', $request->emp_id],
                    ['isDelete', '=', 0]
                ]);
                
            if ($fromDateFormatted) {
                $dealDone->where('created_at', '>=', $fromDateFormatted);
            }
    
            if ($toDateFormatted) {
                $dealDone->where('created_at', '<=', $toDateFormatted);
            }

            // Deal Cancel Query
            $dealCancel = DB::table('deal_cancel')
                ->select(array_merge($commonSelect, [
                    DB::raw("'deal_cancel' as source")
                ]))
                ->where([
                    ['iCustomerId', '=', $employee->company_id],
                    ['iEnterBy', '=', $request->emp_id],
                    ['isDelete', '=', 0]
                ]);
            
            if ($fromDateFormatted) {
                $dealCancel->where('created_at', '>=', $fromDateFormatted);
            }
    
            if ($toDateFormatted) {
                $dealCancel->where('created_at', '<=', $toDateFormatted);
            }    

            // Combine all queries with UNION
            $combinedLeads = $leadMaster
                ->unionAll($dealDone)
                ->unionAll($dealCancel);

            // Run query and join to get Lead Source and Service name
            $leads = DB::table(DB::raw("({$combinedLeads->toSql()}) as all_leads"))
                ->mergeBindings($combinedLeads)
                ->leftJoin('lead_source_master', 'lead_source_master.lead_source_id', '=', 'all_leads.LeadSourceId')
                ->leftJoin('service_master', 'service_master.service_id', '=', 'all_leads.product_service_id')
                ->select(
                    'all_leads.lead_id',
                    'all_leads.company_name',
                    'all_leads.customer_name',
                    'all_leads.email',
                    'all_leads.mobile',
                    'lead_source_master.lead_source_name',
                    'service_master.service_name',
                    'all_leads.source',
                    'all_leads.created_at'
                )
                ->orderByDesc('all_leads.created_at')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $leads,
            ]);
        } catch (\Exception $e) {
            Log::error('API Lead Generated Detail Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Internal server error.'], 500);
        }
    }

    public function lead_given_detail(Request $request)
    {
        try {
            $request->validate([
                'emp_id' => 'required'
            ]);

            $employee = Auth::guard('employee_api')->user();
        
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            
            $fromDateFormatted = $fromDate ? Carbon::createFromFormat('d-m-Y', $fromDate)->startOfDay() : null;
            $toDateFormatted = $toDate ? Carbon::createFromFormat('d-m-Y', $toDate)->endOfDay() : null;
          
            // Common SELECT fields from all tables
            $commonSelect = [
                'lead_id',
                'company_name',
                'customer_name',
                'email',
                'mobile',
                'LeadSourceId',
                'product_service_id',
                'created_at',
            ];

            // Lead Master Query
            $leadMaster = DB::table('lead_master')
                ->select(array_merge($commonSelect, [
                    DB::raw("'lead_master' as source")
                ]))
                ->where([
                    ['iCustomerId', '=', $employee->company_id],
                    ['iemployeeId', '=', $request->emp_id],
                    ['isDelete', '=', 0]
                ]);
            
            if ($fromDateFormatted) {
                $leadMaster->where('created_at', '>=', $fromDateFormatted);
            }
    
            if ($toDateFormatted) {
                $leadMaster->where('created_at', '<=', $toDateFormatted);
            }    

            // Deal Done Query
            $dealDone = DB::table('deal_done')
                ->select(array_merge($commonSelect, [
                    DB::raw("'deal_done' as source")
                ]))
                ->where([
                    ['iCustomerId', '=', $employee->company_id],
                    ['iemployeeId', '=', $request->emp_id],
                    ['isDelete', '=', 0]
                ]);
                
            if ($fromDateFormatted) {
                $dealDone->where('created_at', '>=', $fromDateFormatted);
            }
    
            if ($toDateFormatted) {
                $dealDone->where('created_at', '<=', $toDateFormatted);
            }    

            // Deal Cancel Query
            $dealCancel = DB::table('deal_cancel')
                ->select(array_merge($commonSelect, [
                    DB::raw("'deal_cancel' as source")
                ]))
                ->where([
                    ['iCustomerId', '=', $employee->company_id],
                    ['iemployeeId', '=', $request->emp_id],
                    ['isDelete', '=', 0]
                ]);
                
            if ($fromDateFormatted) {
                $dealCancel->where('created_at', '>=', $fromDateFormatted);
            }
    
            if ($toDateFormatted) {
                $dealCancel->where('created_at', '<=', $toDateFormatted);
            }    

            // Combine all queries with UNION
            $combinedLeads = $leadMaster
                ->unionAll($dealDone)
                ->unionAll($dealCancel);

            // Run query and join to get Lead Source and Service name
            $leads = DB::table(DB::raw("({$combinedLeads->toSql()}) as all_leads"))
                ->mergeBindings($combinedLeads)
                ->leftJoin('lead_source_master', 'lead_source_master.lead_source_id', '=', 'all_leads.LeadSourceId')
                ->leftJoin('service_master', 'service_master.service_id', '=', 'all_leads.product_service_id')
                ->select(
                    'all_leads.lead_id',
                    'all_leads.company_name',
                    'all_leads.customer_name',
                    'all_leads.email',
                    'all_leads.mobile',
                    'lead_source_master.lead_source_name',
                    'service_master.service_name',
                    'all_leads.source',
                    'all_leads.created_at'
                )
                ->orderByDesc('all_leads.created_at')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $leads
            ]);
        } catch (\Exception $e) {
            Log::error('API Lead Given Detail Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Internal server error.'], 500);
        }
    }

    public function lead_analysis_detail(Request $request)
    {
        try {

            $request->validate([
                'lead_source_id' => 'required',
                'pipeline_id' => 'required',
            ]);
            $employee = Auth::guard('employee_api')->user();
            
            // Parse and format the optional from_date and to_date
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
    
            $fromDateFormatted = $fromDate ? Carbon::createFromFormat('d-m-Y', $fromDate)->startOfDay() : null;
            $toDateFormatted = $toDate ? Carbon::createFromFormat('d-m-Y', $toDate)->endOfDay() : null;


            $lead_pipeline = LeadPipeline::where(['pipeline_id' => $request->pipeline_id])->first();
            
            // Initialize query builder
            if ($lead_pipeline->pipeline_name === "Deal Done") {
                $query = DB::table('deal_done as lm');
            } elseif ($lead_pipeline->pipeline_name === "Deal Cancel") {
                $query = DB::table('deal_cancel as lm');
            } else {
                $query = DB::table('lead_master as lm');
            }
            
            $query->select(
                'lm.lead_id',
                'lm.customer_name',
                'lm.amount',
                'lm.status',
                'lp.pipeline_name',
                'ls.lead_source_name',
                'emp.emp_name as employee_name',
                'lm.created_at'
            )
            ->leftJoin('lead_pipeline_master as lp', 'lp.pipeline_id', '=', 'lm.status')
            ->leftJoin('lead_source_master as ls', 'ls.lead_source_id', '=', 'lm.LeadSourceId')
            ->leftJoin('employee_master as emp', 'emp.emp_id', '=', 'lm.iemployeeId')
            ->where('lm.LeadSourceId', $request->lead_source_id)
            ->where('lm.status', $request->pipeline_id)
            ->where('lm.iCustomerId', $employee->company_id)
            ->where('lm.isDelete', 0)
            ->orderByDesc('lm.created_at');

            // Apply date filters if provided
            if ($fromDateFormatted) {
                $query->where('lm.created_at', '>=', $fromDateFormatted);
            }
    
            if ($toDateFormatted) {
                $query->where('lm.created_at', '<=', $toDateFormatted);
            }
    
            $leads = $query->get();

            // if ($lead_pipeline->pipeline_name === "Deal Done") {
            //     $query = DB::table('deal_done as lm')
            //         ->select(
            //             'lm.lead_id',
            //             'lm.customer_name',
            //             'lm.amount',
            //             'lm.status',
            //             'lp.pipeline_name',
            //             'ls.lead_source_name',
            //             'emp.emp_name as employee_name',
            //             'lm.created_at'
            //         )
            //         ->leftJoin('lead_pipeline_master as lp', 'lp.pipeline_id', '=', 'lm.status')
            //         ->leftJoin('lead_source_master as ls', 'ls.lead_source_id', '=', 'lm.LeadSourceId')
            //         ->leftJoin('employee_master as emp', 'emp.emp_id', '=', 'lm.iemployeeId')
            //         ->where('lm.LeadSourceId', $request->lead_source_id)
            //         ->where('lm.status', $request->pipeline_id)
            //         ->where('lm.iCustomerId', $employee->company_id)
            //         ->where('lm.isDelete', 0)
            //         ->orderByDesc('lm.created_at');
            // } elseif ($lead_pipeline->pipeline_name === "Deal Cancel") {
            //     $query = DB::table('deal_cancel as lm')
            //         ->select(
            //             'lm.lead_id',
            //             'lm.customer_name',
            //             'lm.amount',
            //             'lm.status',
            //             'lp.pipeline_name',
            //             'ls.lead_source_name',
            //             'emp.emp_name as employee_name',
            //             'lm.created_at'
            //         )
            //         ->leftJoin('lead_pipeline_master as lp', 'lp.pipeline_id', '=', 'lm.status')
            //         ->leftJoin('lead_source_master as ls', 'ls.lead_source_id', '=', 'lm.LeadSourceId')
            //         ->leftJoin('employee_master as emp', 'emp.emp_id', '=', 'lm.iemployeeId')
            //         ->where('lm.LeadSourceId', $request->lead_source_id)
            //         ->where('lm.status', $request->pipeline_id)
            //         ->where('lm.iCustomerId', $employee->company_id)
            //         ->where('lm.isDelete', 0)
            //         ->orderByDesc('lm.created_at');
            // } else {
            //     $query = DB::table('lead_master as lm')
            //         ->select(
            //             'lm.lead_id',
            //             'lm.customer_name',
            //             'lm.amount',
            //             'lm.status',
            //             'lp.pipeline_name',
            //             'ls.lead_source_name',
            //             'emp.emp_name as employee_name',
            //             'lm.created_at'
            //         )
            //         ->leftJoin('lead_pipeline_master as lp', 'lp.pipeline_id', '=', 'lm.status')
            //         ->leftJoin('lead_source_master as ls', 'ls.lead_source_id', '=', 'lm.LeadSourceId')
            //         ->leftJoin('employee_master as emp', 'emp.emp_id', '=', 'lm.iemployeeId')
            //         ->where('lm.LeadSourceId', $request->lead_source_id)
            //         ->where('lm.status', $request->pipeline_id)
            //         ->where('lm.iCustomerId', $employee->company_id)
            //         ->where('lm.isDelete', 0)
            //         ->orderByDesc('lm.created_at');
            // }

            // $leads = $query->get();

            // Get detailed leads under the selected lead source
            // $leads = DB::table('lead_master as lm')
            //     ->select(
            //         'lm.lead_id',
            //         'lm.customer_name',
            //         'lm.amount',
            //         'lm.status',
            //         'lp.pipeline_name',
            //         'ls.lead_source_name',
            //         'emp.emp_name as employee_name',
            //         'lm.created_at'
            //     )
            //     ->leftJoin('lead_pipeline_master as lp', 'lp.pipeline_id', '=', 'lm.status')
            //     ->leftJoin('lead_source_master as ls', 'ls.lead_source_id', '=', 'lm.LeadSourceId')
            //     ->leftJoin('employee_master as emp', 'emp.emp_id', '=', 'lm.iemployeeId')
            //     ->where('lm.LeadSourceId', $request->lead_source_id)
            //     ->where('lm.status', $request->pipeline_id)
            //     ->where('lm.iCustomerId', $employee->company_id)
            //     ->where('lm.isDelete', 0)
            //     ->orderByDesc('lm.created_at')
            //     ->get();

            return response()->json([
                'success' => true,
                'data' => $leads
            ]);
        } catch (\Exception $e) {
            Log::error('API Lead Analysis Detail Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error.'
            ], 500);
        }
    }

    public function lead_cancel_analysis_detail(Request $request)
    {
        try {
            $request->validate([
                'cancel_reason_id' => 'required|integer'
            ]);

            $employee = Auth::guard('employee_api')->user();
            $companyId = $employee->company_id;

            // Get cancelled pipeline ID
            $cancelledPipelineId = LeadPipeline::where('slugname', 'deal-cancel')
                ->where('company_id', $companyId)
                ->value('pipeline_id');

            // Base query
            $query = DB::table('deal_cancel as dc')
                ->leftJoin('lead_source_master as lsm', 'lsm.lead_source_id', '=', 'dc.LeadSourceId')
                ->leftJoin('service_master as sm', 'sm.service_id', '=', 'dc.product_service_id')
                ->leftJoin('lead_cancel_reason as cr', 'cr.lead_cancel_reason_id', '=', 'dc.cancel_reason_id')
                ->select(
                    'dc.lead_id',
                    'dc.company_name',
                    'dc.customer_name',
                    'dc.email',
                    'dc.mobile',
                    'lsm.lead_source_name',
                    'sm.service_name',
                    'cr.reason as cancel_reason',
                    'dc.created_at'
                )
                ->where('dc.iCustomerId', $companyId)
                ->where('dc.isDelete', 0)
                ->where('dc.status', $cancelledPipelineId)
                ->where('dc.cancel_reason_id', $request->cancel_reason_id);
                
            // Apply date filtering if provided
            if (!empty($request->from_date) && !empty($request->to_date)) {
                $from = \Carbon\Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay();
                $to = \Carbon\Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay();
    
                $query->whereBetween('dc.created_at', [$from, $to]);
            }    

            $leads = $query->orderByDesc('dc.created_at')->get();

            return response()->json([
                'success' => true,
                'data' => $leads,
            ]);
        } catch (\Exception $e) {
            Log::error('API Lead Cancel Detail Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Internal server error.'], 500);
        }
    }

    protected function handleExcelExport(array $data, string $prefix, array $headings = [])
    {
        $token = Str::uuid()->toString();

        // Store both data + headings as an array
        Cache::put("report_excel_{$token}", [
            'data' => $data,
            'headings' => $headings
        ]);

        $url = route('report.download', ['token' => $token, 'filename' => "{$prefix}_report.xlsx"]);

        return response()->json([
            'success' => true,
            'download_url' => $url
        ]);
    }

    public function downloadExcel($token, $filename = 'report.xlsx')
    {
        $cached = Cache::get("report_excel_{$token}");

        if (!$cached || !isset($cached['data'])) {
            return response()->json(['success' => false, 'message' => 'Download link expired or invalid.'], 404);
        }

        $data = $cached['data'];
        $headings = $cached['headings'] ?? [];

        return Excel::download(new ReportExport($data, $headings), $filename);
    }
}
