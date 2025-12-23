<?php

namespace App\Http\Controllers\Company;

use App\Exports\EmpPerformanceExport;
use App\Exports\LeadAnalysisExport;
use App\Exports\LeadCancelAnalysisExport;
use App\Exports\ROIReportExport;
use App\Http\Controllers\Controller;
use App\Models\DealCancel;
use App\Models\DealDone;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeadMaster;
use App\Models\LeadPipeline;
use App\Models\LeadSource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportController extends Controller
{

    public function roi_report(Request $request)
    {
        try {
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $companyId = Auth::user()->company_id;

            // Get pipeline ID for 'deal-done' (converted leads)
            $convertedPipeline = LeadPipeline::where('company_id', $companyId)
                ->where('slugname', 'deal-done')
                ->value('pipeline_id');

            // Get all lead sources
            $leadSources = LeadSource::where('company_id', $companyId)->get();

            $reportData = [];

            foreach ($leadSources as $source) {
                // ---------------------------
                // Leads from lead_master
                // ---------------------------
                $leadMasterQuery = DB::table('lead_master')
                    ->where('isDelete', 0)
                    ->where('iCustomerId', $companyId)
                    ->where('LeadSourceId', $source->lead_source_id);

                if ($fromDate) {
                    $leadMasterQuery->whereDate('created_at', '>=', $fromDate);
                }
                if ($toDate) {
                    $leadMasterQuery->whereDate('created_at', '<=', $toDate);
                }

                $leadMasterCount = $leadMasterQuery->count();

                // ---------------------------
                // Leads from deal_done
                // ---------------------------
                $dealDoneQuery = DB::table('deal_done')
                    ->where('isDelete', 0)
                    ->where('iCustomerId', $companyId)
                    ->where('LeadSourceId', $source->lead_source_id);

                if ($fromDate) {
                    $dealDoneQuery->whereDate('created_at', '>=', $fromDate);
                }
                if ($toDate) {
                    $dealDoneQuery->whereDate('created_at', '<=', $toDate);
                }

                $dealDoneCount = $dealDoneQuery->count();
                $dealDoneAmount = $dealDoneQuery->sum('amount');

                $reportData[] = [
                    'lead_source_id' => $source->lead_source_id,
                    'source_name' => $source->lead_source_name,
                    'leads_found' => $leadMasterCount + $dealDoneCount, // From both tables
                    'leads_converted' => $dealDoneCount,                // Only from deal_done
                    'converted_amount' => round($dealDoneAmount, 2),    // Only from deal_done
                ];
            }

            return view('company_client.reports.ROI', compact('reportData', 'fromDate', 'toDate'));
        } catch (\Exception $e) {
            Log::error('Error in roi_report: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }


    public function exportROIReport(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $companyId = Auth::user()->company_id;

        // Get pipeline ID for 'deal-done' (converted leads)
        $convertedPipeline = LeadPipeline::where('company_id', $companyId)
            ->where('slugname', 'deal-done')
            ->value('pipeline_id');

        // Get all lead sources
        $leadSources = LeadSource::where('company_id', $companyId)->get();

        $reportData = [];

        foreach ($leadSources as $source) {
            // ---------------------------
            // Leads from lead_master
            // ---------------------------
            $leadMasterQuery = DB::table('lead_master')
                ->where('isDelete', 0)
                ->where('iCustomerId', $companyId)
                ->where('LeadSourceId', $source->lead_source_id);

            if ($fromDate) {
                $leadMasterQuery->whereDate('created_at', '>=', $fromDate);
            }
            if ($toDate) {
                $leadMasterQuery->whereDate('created_at', '<=', $toDate);
            }

            $leadMasterCount = $leadMasterQuery->count();

            // ---------------------------
            // Leads from deal_done
            // ---------------------------
            $dealDoneQuery = DB::table('deal_done')
                ->where('isDelete', 0)
                ->where('iCustomerId', $companyId)
                ->where('LeadSourceId', $source->lead_source_id);

            if ($fromDate) {
                $dealDoneQuery->whereDate('created_at', '>=', $fromDate);
            }
            if ($toDate) {
                $dealDoneQuery->whereDate('created_at', '<=', $toDate);
            }

            $dealDoneCount = $dealDoneQuery->count();
            $dealDoneAmount = $dealDoneQuery->sum('amount');

            $reportData[] = [
                'source_name' => $source->lead_source_name,
                'leads_found' => $leadMasterCount + $dealDoneCount, // From both tables
                'leads_converted' => $dealDoneCount,                // Only from deal_done
                'converted_amount' => round($dealDoneAmount, 2),    // Only from deal_done
            ];
        }

        return Excel::download(new ROIReportExport($reportData), 'roi_report.xlsx');
    }

    public function emp_performance(Request $request)
    {
        try {
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $search = $request->input('emp_name');
            $companyId = Auth::user()->company_id;

            // Get converted pipeline ID (lead-done)
            $convertedPipelineId = LeadPipeline::where('slugname', 'deal-done')
                ->where('company_id', $companyId)
                ->value('pipeline_id');

            $allemployees = Employee::where('company_id', $companyId)->get();

            // Get employees matching company and name filter
            $employees = Employee::where('company_id', $companyId)
                ->when($search, function ($query, $search) {
                    return $query->where('emp_id', '=', $search);
                })
                ->paginate(config('app.per_page'));


            // Loop each employee and calculate stats
            foreach ($employees as $emp) {
                // Leads generated by employee (iEnterBy)

                $generatedQuery = LeadMaster::where('iCustomerId', $companyId)
                    ->where('iEnterBy', $emp->emp_id)
                    ->where('isDelete', 0);
                $generatedQuery1 = DealDone::where('iCustomerId', $companyId)
                    ->where('iEnterBy', $emp->emp_id)
                    ->where('isDelete', 0);
                $generatedQuery2 = DealCancel::where('iCustomerId', $companyId)
                    ->where('iEnterBy', $emp->emp_id)
                    ->where('isDelete', 0);

                // if ($fromDate) $generatedQuery->whereDate('created_at', '>=', $fromDate);
                // if ($toDate) $generatedQuery->whereDate('created_at', '<=', $toDate);
                
                if ($fromDate) {
                    $generatedQuery->whereDate('created_at', '>=',  $fromDate);
                    $generatedQuery1->whereDate('created_at', '>=', $fromDate);
                    $generatedQuery2->whereDate('created_at', '>=', $fromDate);
                }
            
                if ($toDate) {
                    $generatedQuery->whereDate('created_at', '<=', $toDate);
                    $generatedQuery1->whereDate('created_at', '<=',  $toDate);
                    $generatedQuery2->whereDate('created_at', '<=', $toDate);
                } 
                
                $emp->leads_generated = $generatedQuery->count() + $generatedQuery1->count() + $generatedQuery2->count();

                // Leads assigned to employee (iemployeeId)
                $assignedQuery = LeadMaster::where('iCustomerId', $companyId)
                    ->where('iemployeeId', $emp->emp_id)
                    ->where('isDelete', 0);
                $assignedQuery1 = DealDone::where('iCustomerId', $companyId)
                    ->where('iemployeeId', $emp->emp_id)
                    ->where('isDelete', 0);
                $assignedQuery2 = DealCancel::where('iCustomerId', $companyId)
                    ->where('iemployeeId', $emp->emp_id)
                    ->where('isDelete', 0);

                // if ($fromDate) $assignedQuery->whereDate('created_at', '>=', $fromDate);
                // if ($toDate) $assignedQuery->whereDate('created_at', '<=', $toDate);
                
                if ($fromDate) {
                    $assignedQuery->whereDate('created_at', '>=',  $fromDate);
                    $assignedQuery1->whereDate('created_at', '>=', $fromDate);
                    $assignedQuery2->whereDate('created_at', '>=', $fromDate);
                }
            
                if ($toDate) {
                    $assignedQuery->whereDate('created_at', '<=', $toDate);
                    $assignedQuery1->whereDate('created_at', '<=',  $toDate);
                    $assignedQuery2->whereDate('created_at', '<=', $toDate);
                } 
                
                $emp->leads_assigned = $assignedQuery->count() + $assignedQuery1->count() + $assignedQuery2->count();

                // Converted amount (from assigned leads where status = convertedPipelineId)

                $convertedQuery = DealDone::where('iCustomerId', $companyId)
                    ->where('iemployeeId', $emp->emp_id)
                    ->where('status', $convertedPipelineId)
                    ->where('isDelete', 0);
                if ($fromDate) $convertedQuery->whereDate('created_at', '>=', $fromDate);
                if ($toDate) $convertedQuery->whereDate('created_at', '<=', $toDate);
                $emp->converted_amount = $convertedQuery->sum('amount');
            }

            return view('company_client.reports.emp_performance', compact('allemployees', 'employees', 'fromDate', 'toDate', 'search'));
        } catch (\Exception $e) {
            Log::error('Error in emp_performance: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function exportEmpPerformance(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate   = $request->input('to_date');
        $search   = $request->input('emp_name');
        $companyId = Auth::user()->company_id;

        // Get converted pipeline ID
        $convertedPipelineId = LeadPipeline::where('slugname', 'deal-done')
            ->where('company_id', $companyId)
            ->value('pipeline_id');

        // Fetch employees
        $employees = Employee::where('company_id', $companyId)
            ->when($search, fn($q) => $q->where('emp_id', '=', $search))
            ->get();

        // Build report data
        $reportData = [];
        foreach ($employees as $emp) {
            // generated
            $genQ = LeadMaster::where('iCustomerId', $companyId)
                ->where('iEnterBy', $emp->emp_id)
                ->where('isDelete', 0);
            $genQ1 = DealDone::where('iCustomerId', $companyId)
                ->where('iEnterBy', $emp->emp_id)
                ->where('isDelete', 0);
            if ($fromDate) $genQ->whereDate('created_at', '>=', $fromDate);
            if ($toDate)   $genQ->whereDate('created_at', '<=', $toDate);
            $leadsGen = $genQ->count() + $genQ1->count();

            // assigned
            $assQ = LeadMaster::where('iCustomerId', $companyId)
                ->where('iemployeeId', $emp->emp_id)
                ->where('isDelete', 0);
            $assQ1 = DealDone::where('iCustomerId', $companyId)
                ->where('iemployeeId', $emp->emp_id)
                ->where('isDelete', 0);
            if ($fromDate) $assQ->whereDate('created_at', '>=', $fromDate);
            if ($toDate)   $assQ->whereDate('created_at', '<=', $toDate);
            $leadsAss = $assQ->count() + $assQ1->count();

            // converted amount
            $convAmt = DealDone::where('iCustomerId', $companyId)
                ->where('iemployeeId', $emp->emp_id)
                ->where('isDelete', 0)
                ->sum('amount');

            $reportData[] = [
                'emp_name'         => $emp->emp_name,
                'leads_generated'  => $leadsGen,
                'leads_assigned'   => $leadsAss,
                'converted_amount' => $convAmt,
            ];
        }


        return Excel::download(new EmpPerformanceExport($reportData), 'employee_performance_report.xlsx');
    }

    public function emp_lead_analysis(Request $request)
    {
        try {

            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $companyId = Auth::user()->company_id;

            // Join lead_master with pipeline and lead source
            $query = DB::table('lead_master as lm')
                ->select(
                    'lm.LeadSourceId',
                    'ls.lead_source_name',
                    'lm.status as pipeline_id',
                    'lp.pipeline_name',
                    DB::raw('COUNT(*) as lead_count'),
                    DB::raw('SUM(lm.amount) as total_amount')
                )
                ->join('lead_source_master as ls', 'ls.lead_source_id', '=', 'lm.LeadSourceId')
                ->join('lead_pipeline_master as lp', 'lp.pipeline_id', '=', 'lm.status')
                ->where('lm.iCustomerId', $companyId)
                ->whereNotIn('lp.slugname', ['deal-done', 'deal-cancel'])
                ->where('lm.isDelete', 0)
                ->groupBy('lm.LeadSourceId', 'lm.status', 'ls.lead_source_name', 'lp.pipeline_name');

            if ($fromDate) {
                $query->whereDate('lm.created_at', '>=', $fromDate);
            }
            if ($toDate) {
                $query->whereDate('lm.created_at', '<=', $toDate);
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
                ->join('lead_source_master as ls', 'ls.lead_source_id', '=', 'lm.LeadSourceId')
                ->join('lead_pipeline_master as lp', 'lp.pipeline_id', '=', 'lm.status')
                ->where('lm.iCustomerId', $companyId)
                ->whereIn('lp.slugname', ['deal-done'])
                ->where('lm.isDelete', 0)
                ->groupBy('lm.LeadSourceId', 'lm.status', 'ls.lead_source_name', 'lp.pipeline_name');
                
            if ($fromDate) {
                $queryDone->whereDate('lm.created_at', '>=', $fromDate);
            }
            if ($toDate) {
                $queryDone->whereDate('lm.created_at', '<=', $toDate);
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
                ->join('lead_source_master as ls', 'ls.lead_source_id', '=', 'lm.LeadSourceId')
                ->join('lead_pipeline_master as lp', 'lp.pipeline_id', '=', 'lm.status')
                ->where('lm.iCustomerId', $companyId)
                ->whereIn('lp.slugname', ['deal-cancel'])
                ->where('lm.isDelete', 0)
                ->groupBy('lm.LeadSourceId', 'lm.status', 'ls.lead_source_name', 'lp.pipeline_name');
                
            if ($fromDate) {
                $queryCancel->whereDate('lm.created_at', '>=', $fromDate);
            }
            if ($toDate) {
                $queryCancel->whereDate('lm.created_at', '<=', $toDate);
            }
            $reportData = $query->union($queryDone)->union($queryCancel)->paginate(config('app.per_page'));
            //$reportData = $query->paginate(10);
           //dd($reportData);

            // $emp_id = Auth::guard('web_employees')->user()->company_id;

            // // Optional date filters
            // $fromDate = $request->input('from_date');
            // $toDate = $request->input('to_date');

            // // Main pipeline leads (not deal-done or deal-cancel)
            // $pipline = DB::table('lead_pipeline_master as lp')
            //     ->select(
            //         'lp.pipeline_id',
            //         'lp.pipeline_name',
            //         'lp.color',
            //         'lp.icon',
            //         'lp.created_at',
            //         'lp.company_id',
            //         'lm.LeadSourceId',
            //         'ls.lead_source_name',
            //         DB::raw('COUNT(*) as lead_count'),
            //         DB::raw('COUNT(lm.lead_id) as status_count'),
            //         DB::raw('SUM(lm.amount) as total_amount')
            //     )
            //     ->leftJoin('lead_master as lm', function ($join) use ($emp_id) {
            //         $join->on('lm.status', '=', 'lp.pipeline_id')
            //             ->where('lm.iCustomerId', $emp_id)
            //             ->where('lm.isDelete', 0);
            //     })
            //     ->leftJoin('lead_source_master as ls', 'ls.lead_source_id', '=', 'lm.LeadSourceId')
            //     ->where('lp.company_id', $emp_id)
            //     ->whereNotIn('lp.slugname', ['deal-done', 'deal-cancel'])
            //     ->when($fromDate, fn($q) => $q->whereDate('lm.created_at', '>=', $fromDate))
            //     ->when($toDate, fn($q) => $q->whereDate('lm.created_at', '<=', $toDate))
            //     ->groupBy(
            //         'lp.pipeline_id',
            //         'lp.pipeline_name',
            //         'lp.color',
            //         'lp.icon',
            //         'lp.created_at',
            //         'lp.company_id',
            //         'lm.LeadSourceId',
            //         'ls.lead_source_name'
            //     );

            // // Deal done
            // $piplineDones = DB::table('lead_pipeline_master as lp')
            //     ->select(
            //         'lp.pipeline_id',
            //         'lp.pipeline_name',
            //         'lp.color',
            //         'lp.icon',
            //         'lp.created_at',
            //         'lp.company_id',
            //         'dd.LeadSourceId',
            //         'ls.lead_source_name',
            //         DB::raw('COUNT(*) as lead_count'),
            //         DB::raw('COUNT(dd.lead_id) as status_count'),
            //         DB::raw('SUM(dd.amount) as total_amount')
            //     )
            //     ->leftJoin('deal_done as dd', function ($join) use ($emp_id) {
            //         $join->on('dd.status', '=', 'lp.pipeline_id')
            //             ->where('dd.iCustomerId', $emp_id)
            //             ->where('dd.isDelete', 0);
            //     })
            //     ->leftJoin('lead_source_master as ls', 'ls.lead_source_id', '=', 'dd.LeadSourceId')
            //     ->where('lp.company_id', $emp_id)
            //     ->where('lp.slugname', 'deal-done')
            //     ->when($fromDate, fn($q) => $q->whereDate('dd.created_at', '>=', $fromDate))
            //     ->when($toDate, fn($q) => $q->whereDate('dd.created_at', '<=', $toDate))
            //     ->groupBy(
            //         'lp.pipeline_id',
            //         'lp.pipeline_name',
            //         'lp.color',
            //         'lp.icon',
            //         'lp.created_at',
            //         'lp.company_id',
            //         'dd.LeadSourceId',
            //         'ls.lead_source_name'
            //     );

            // // Deal cancel
            // $piplineCancels = DB::table('lead_pipeline_master as lp')
            //     ->select(
            //         'lp.pipeline_id',
            //         'lp.pipeline_name',
            //         'lp.color',
            //         'lp.icon',
            //         'lp.created_at',
            //         'lp.company_id',
            //         'dc.LeadSourceId',
            //         'ls.lead_source_name',
            //         DB::raw('COUNT(*) as lead_count'),
            //         DB::raw('COUNT(dc.lead_id) as status_count'),
            //         DB::raw('SUM(dc.amount) as total_amount')
            //     )
            //     ->leftJoin('deal_cancel as dc', function ($join) use ($emp_id) {
            //         $join->on('dc.status', '=', 'lp.pipeline_id')
            //             ->where('dc.iCustomerId', $emp_id)
            //             ->where('dc.isDelete', 0);
            //     })
            //     ->leftJoin('lead_source_master as ls', 'ls.lead_source_id', '=', 'dc.LeadSourceId')
            //     ->where('lp.company_id', $emp_id)
            //     ->where('lp.slugname', 'deal-cancel')
            //     ->when($fromDate, fn($q) => $q->whereDate('dc.created_at', '>=', $fromDate))
            //     ->when($toDate, fn($q) => $q->whereDate('dc.created_at', '<=', $toDate))
            //     ->groupBy(
            //         'lp.pipeline_id',
            //         'lp.pipeline_name',
            //         'lp.color',
            //         'lp.icon',
            //         'lp.created_at',
            //         'lp.company_id',
            //         'dc.LeadSourceId',
            //         'ls.lead_source_name'
            //     );

            // // Final result (merged)
            // $reportData = $pipline
            //     ->unionAll($piplineDones)
            //     ->unionAll($piplineCancels)
            //     ->get();

            // // Filter only required fields
            // $reportData = $reportData->map(function ($item) {
            //     return [
            //         'pipeline_id'      => $item->pipeline_id,
            //         'pipeline_name'    => $item->pipeline_name,
            //         'LeadSourceId'     => $item->LeadSourceId,
            //         'lead_source_name' => $item->lead_source_name,
            //         'total_amount' => $item->total_amount,
            //     ];
            // });
            // dd($reportData);

            return view('company_client.reports.lead_analysis', compact('reportData', 'fromDate', 'toDate'));
        } catch (\Exception $e) {
            Log::error('Error in emp_lead_analysis: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function exportLeadAnalysis(Request $request)
    {
        
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $companyId = Auth::user()->company_id;

        // Join lead_master with pipeline and lead source
        $query = DB::table('lead_master as lm')
            ->select(
                'lm.LeadSourceId',
                'ls.lead_source_name',
                'lm.status as pipeline_id',
                'lp.pipeline_name',
                DB::raw('COUNT(*) as lead_count'),
                DB::raw('SUM(lm.amount) as total_amount')
            )
            ->join('lead_source_master as ls', 'ls.lead_source_id', '=', 'lm.LeadSourceId')
            ->join('lead_pipeline_master as lp', 'lp.pipeline_id', '=', 'lm.status')
            ->where('lm.iCustomerId', $companyId)
            ->whereNotIn('lp.slugname', ['deal-done', 'deal-cancel'])
            ->where('lm.isDelete', 0)
            ->groupBy('lm.LeadSourceId', 'lm.status', 'ls.lead_source_name', 'lp.pipeline_name');

        if ($fromDate) {
            $query->whereDate('lm.created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('lm.created_at', '<=', $toDate);
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
            ->join('lead_source_master as ls', 'ls.lead_source_id', '=', 'lm.LeadSourceId')
            ->join('lead_pipeline_master as lp', 'lp.pipeline_id', '=', 'lm.status')
            ->where('lm.iCustomerId', $companyId)
            ->whereIn('lp.slugname', ['deal-done'])
            ->where('lm.isDelete', 0)
            ->groupBy('lm.LeadSourceId', 'lm.status', 'ls.lead_source_name', 'lp.pipeline_name');
        if ($fromDate) {
            $queryDone->whereDate('lm.created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $queryDone->whereDate('lm.created_at', '<=', $toDate);
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
            ->join('lead_source_master as ls', 'ls.lead_source_id', '=', 'lm.LeadSourceId')
            ->join('lead_pipeline_master as lp', 'lp.pipeline_id', '=', 'lm.status')
            ->where('lm.iCustomerId', $companyId)
            ->whereIn('lp.slugname', ['deal-cancel'])
            ->where('lm.isDelete', 0)
            ->groupBy('lm.LeadSourceId', 'lm.status', 'ls.lead_source_name', 'lp.pipeline_name');
        if ($fromDate) {
            $queryCancel->whereDate('lm.created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $queryCancel->whereDate('lm.created_at', '<=', $toDate);
        }
        $reportData = $query->union($queryDone)->union($queryCancel)->get();

        return Excel::download(new LeadAnalysisExport($reportData), 'lead_analysis.xlsx');
    }
    
    public function lead_analysis_detail(Request $request, $lead_source_id, $pipeline_id)
    {
        try {
            
            // dd([$lead_source_id, $pipeline_id]);

            $companyId = Auth::user()->company_id;
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            $lead_pipeline = LeadPipeline::where(['pipeline_id' => $pipeline_id])->first();
            
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
            ->where('lm.LeadSourceId', $lead_source_id)
            ->where('lm.status', $pipeline_id)
            ->where('lm.iCustomerId', $companyId)
            ->where('lm.isDelete', 0)
            ->orderBy('lm.lead_id','desc');
            // dd($query);
            
            // Apply date filters if provided
            if ($fromDate) {
                $query->whereDate('lm.created_at', '>=', $fromDate);
            }
    
            if ($toDate) {
                $query->whereDate('lm.created_at', '<=', $toDate);
            }

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
            //         ->where('lm.LeadSourceId', $lead_source_id)
            //         ->where('lm.status', $pipeline_id)
            //         ->where('lm.iCustomerId', $companyId)
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
            //         ->where('lm.LeadSourceId', $lead_source_id)
            //         ->where('lm.status', $pipeline_id)
            //         ->where('lm.iCustomerId', $companyId)
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
            //         ->where('lm.LeadSourceId', $lead_source_id)
            //         ->where('lm.status', $pipeline_id)
            //         ->where('lm.iCustomerId', $companyId)
            //         ->where('lm.isDelete', 0)
            //         ->orderByDesc('lm.created_at');
            // }

            $reportData = $query->paginate(config('app.per_page'));
            // dd($reportData);

            return view('company_client.reports.lead_analysis_detail', compact('reportData','fromDate','toDate'));
        } catch (\Exception $e) {
            Log::error('Error in emp_lead_analysis: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function emp_lead_cancel_analysis(Request $request)
    {
        // try {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $companyId = Auth::user()->company_id;

        // Get the pipeline_id for cancelled leads
        $cancelledPipelineId = LeadPipeline::where('slugname', 'deal-cancel')
            ->where('company_id', $companyId)
            ->value('pipeline_id');

        // Fetch lead counts by source where status = cancelled
        $query = DB::table('deal_cancel as lm')
            ->select(
                'lm.cancel_reason_id',
                'cr.reason',
                DB::raw('COUNT(*) as lead_count')
            )
            ->leftJoin('lead_cancel_reason as cr', 'cr.lead_cancel_reason_id', '=', 'lm.cancel_reason_id')
            ->where('lm.iCustomerId', $companyId)
            ->where('lm.isDelete', 0)
            ->where('lm.status', $cancelledPipelineId);

        if ($fromDate) {
            $query->whereDate('lm.created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('lm.created_at', '<=', $toDate);
        }

        $query->groupBy('cr.reason', 'lm.cancel_reason_id');

        // $reportData = $query->paginate(10);


        // For chart: get all data and calculate total + percentage
        $allData = $query->get();
        $totalLeads = $allData->sum('lead_count');

        foreach ($allData as $row) {
            $row->percentage = $totalLeads > 0 ? round(($row->lead_count / $totalLeads) * 100, 2) : 0;
        }

        $chartLabels = $allData->pluck('reason');
        $chartPercentages = $allData->pluck('percentage');

        // For table: get paginated data
        $reportData = $query->paginate(config('app.per_page'));
        // dd($chartLabels);

        return view('company_client.reports.lead_cancel_analysis', compact(
            'reportData',
            'fromDate',
            'toDate',
            'chartLabels',
            'chartPercentages'
        ));
        // } catch (\Exception $e) {
        //     Log::error('Error in emp_lead_cancel_analysis: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        //     return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        // }
    }

    public function lead_cancel_analysis_detail(Request $request, $cancel_reason_id)
    {
        try {
            $companyId = Auth::user()->company_id;
            
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

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
                ->orderBy('dc.lead_id','desc')
                ->where('dc.iCustomerId', $companyId)
                ->where('dc.isDelete', 0)
                ->where('dc.status', $cancelledPipelineId)
                ->where('dc.cancel_reason_id', $request->cancel_reason_id);
                
                // Apply date filters if provided
                if ($fromDate) {
                    $query->whereDate('dc.created_at', '>=', $fromDate);
                }
        
                if ($toDate) {
                    $query->whereDate('dc.created_at', '<=', $toDate);
                }

            $leads = $query->orderByDesc('dc.created_at')->paginate(config('app.per_page'));
            // dd($leads);
            return view('company_client.reports.lead_cancel_analysis_detail', compact('leads','fromDate', 'toDate'));
        } catch (\Exception $e) {
            Log::error('API Lead Cancel Detail Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }


    public function exportLeadCancelAnalysis(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $companyId = Auth::user()->company_id;

        // Get the pipeline_id for cancelled leads
        $cancelledPipelineId = LeadPipeline::where('slugname', 'deal-cancel')
            ->where('company_id', $companyId)
            ->value('pipeline_id');

        // Fetch lead counts by source where status = cancelled
        $query = DB::table('deal_cancel as lm')
            ->select(
                'cr.reason',
                DB::raw('COUNT(*) as lead_count')
            )
            ->leftJoin('lead_cancel_reason as cr', 'cr.lead_cancel_reason_id', '=', 'lm.cancel_reason_id')
            ->where('lm.iCustomerId', $companyId)
            ->where('lm.isDelete', 0)
            ->where('lm.status', $cancelledPipelineId);

        if ($fromDate) {
            $query->whereDate('lm.created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('lm.created_at', '<=', $toDate);
        }

        $query->groupBy('cr.reason');

        $reportData = $query->get();

        return Excel::download(new LeadCancelAnalysisExport($reportData), 'lead_cancel_analysis.xlsx');
    }

    // public function lead_generated_detail(Request $request, $emp_id)
    // {
    //     $employee = Employee::findOrFail($emp_id);
    //     $companyId = Auth::user()->company_id;

    //     $leads = LeadMaster::select(
    //         'lead_master.lead_id',
    //         'lead_master.company_name',
    //         'lead_master.customer_name',
    //         'lead_master.email',
    //         'lead_master.mobile',
    //         'lead_source_master.lead_source_name',
    //         'service_master.service_name',
    //     )
    //         ->where(['iCustomerId' => $companyId, 'iEnterBy' => $emp_id, 'lead_master.isDelete' => 0])
    //         ->leftjoin('lead_source_master', 'lead_source_master.lead_source_id', '=', 'lead_master.lead_id')
    //         ->leftjoin('service_master', 'service_master.service_id', '=', 'lead_master.product_service_id')
    //         ->orderByDesc('lead_master.created_at')
    //         ->paginate(10);

    //     return view('company_client.reports.lead_generated_detail', compact('employee', 'leads'));
    // }

    public function lead_generated_detail(Request $request, $emp_id)
    {
        $employee = Employee::findOrFail($emp_id);
        $companyId = Auth::user()->company_id;
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

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
                ['iCustomerId', '=', $companyId],
                ['iEnterBy', '=', $emp_id],
                ['isDelete', '=', 0]
            ]);

        // Deal Done Query
        $dealDone = DB::table('deal_done')
            ->select(array_merge($commonSelect, [
                DB::raw("'deal_done' as source")
            ]))
            ->where([
                ['iCustomerId', '=', $companyId],
                ['iEnterBy', '=', $emp_id],
                ['isDelete', '=', 0]
            ]);

        // Deal Cancel Query
        $dealCancel = DB::table('deal_cancel')
            ->select(array_merge($commonSelect, [
                DB::raw("'deal_cancel' as source")
            ]))
            ->where([
                ['iCustomerId', '=', $companyId],
                ['iEnterBy', '=', $emp_id],
                ['isDelete', '=', 0]
            ]);
            
        if ($fromDate) {
            $leadMaster->whereDate('created_at', '>=',  $fromDate);
            $dealDone->whereDate('created_at', '>=', $fromDate);
            $dealCancel->whereDate('created_at', '>=', $fromDate);
        }
    
        if ($toDate) {
            $leadMaster->whereDate('created_at', '<=', $toDate);
            $dealDone->whereDate('created_at', '<=',  $toDate);
            $dealCancel->whereDate('created_at', '<=', $toDate);
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
            ->orderBy('all_leads.lead_id','desc')
            ->paginate(config('app.per_page'));

        return view('company_client.reports.lead_generated_detail', compact('employee', 'leads','fromDate','toDate'));
    }

    public function lead_given_detail(Request $request, $emp_id)
    {
        $employee = Employee::findOrFail($emp_id);
        $companyId = Auth::user()->company_id;
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

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
                ['iCustomerId', '=', $companyId],
                ['iemployeeId', '=', $emp_id],
                ['isDelete', '=', 0]
            ]);

        // Deal Done Query
        $dealDone = DB::table('deal_done')
            ->select(array_merge($commonSelect, [
                DB::raw("'deal_done' as source")
            ]))
            ->where([
                ['iCustomerId', '=', $companyId],
                ['iemployeeId', '=', $emp_id],
                ['isDelete', '=', 0]
            ]);

        // Deal Cancel Query
        $dealCancel = DB::table('deal_cancel')
            ->select(array_merge($commonSelect, [
                DB::raw("'deal_cancel' as source")
            ]))
            ->where([
                ['iCustomerId', '=', $companyId],
                ['iemployeeId', '=', $emp_id],
                ['isDelete', '=', 0]
            ]);
            
        if ($fromDate) {
            $leadMaster->whereDate('created_at', '>=',  $fromDate);
            $dealDone->whereDate('created_at', '>=', $fromDate);
            $dealCancel->whereDate('created_at', '>=', $fromDate);
        }
    
        if ($toDate) {
            $leadMaster->whereDate('created_at', '<=', $toDate);
            $dealDone->whereDate('created_at', '<=',  $toDate);
            $dealCancel->whereDate('created_at', '<=', $toDate);
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
            ->orderBy('all_leads.lead_id','desc')
            ->paginate(config('app.per_page'));

        return view('company_client.reports.lead_given_detail', compact('employee', 'leads','fromDate','toDate'));
    }

    // public function lead_given_detail(Request $request, $emp_id)
    // {
    //     $employee = Employee::findOrFail($emp_id);
    //     $companyId = Auth::user()->company_id;

    //     $leads = LeadMaster::select(
    //         'lead_master.lead_id',
    //         'lead_master.company_name',
    //         'lead_master.customer_name',
    //         'lead_master.email',
    //         'lead_master.mobile',
    //         'lead_source_master.lead_source_name',
    //         'service_master.service_name',
    //     )
    //         ->where(['iCustomerId' => $companyId,  'iemployeeId' => $emp_id])
    //         ->leftjoin('lead_source_master', 'lead_source_master.lead_source_id', '=', 'lead_master.lead_id')
    //         ->leftjoin('service_master', 'service_master.service_id', '=', 'lead_master.product_service_id')
    //         ->where('lead_master.isDelete', 0)
    //         ->paginate(10);

    //     return view('company_client.reports.lead_given_detail', compact('employee', 'leads'));
    // }

    


    public function lead_found_detail(Request $request, $lead_source_id)
    {
        try {

            $companyId = Auth::user()->company_id;
            $sourceId = $lead_source_id;
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

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
            
            if ($fromDate) {
                $leadMaster->whereDate('created_at', '>=', $fromDate);
            }
            if ($toDate) {
                $leadMaster->whereDate('created_at', '<=', $toDate);
            }    


            // Deal Done
            $dealDone = DB::table('deal_done')
                ->select(array_merge($commonSelect, [
                    DB::raw("'deal_done' as source")
                ]))
                ->where('isDelete', 0)
                ->where('iCustomerId', $companyId)
                ->where('LeadSourceId', $sourceId);
            
            if ($fromDate) {
                $dealDone->whereDate('created_at', '>=', $fromDate);
            }
            if ($toDate) {
                $dealDone->whereDate('created_at', '<=', $toDate);
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
                ->orderBy('all_leads.lead_id','desc')
                ->paginate(config('app.per_page'));
            // dd($leads);

            return view('company_client.reports.lead_found_detail', compact('leads', 'fromDate', 'toDate'));
        } catch (\Exception $e) {
            Log::error('API Lead Found Detail Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Internal server error.'], 500);
        }
    }


    public function lead_converted_detail(Request $request, $lead_source_id)
    {
        try {
            $companyId = Auth::user()->company_id;
            $sourceId = $lead_source_id;
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

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
                'deal_done_at',
            ];

            // Deal Done Query (Converted Leads)
            $dealDone = DB::table('deal_done')
                ->select(array_merge($selectFields, [
                    DB::raw("'deal_done' as source")
                ]))
                ->where('isDelete', 0)
                ->where('iCustomerId', $companyId)
                ->where('LeadSourceId', $sourceId);
            
            if ($fromDate) {
                $dealDone->whereDate('created_at', '>=', $fromDate);
            }
            if ($toDate) {
                $dealDone->whereDate('created_at', '<=', $toDate);
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
                    'converted_leads.created_at',
                    'converted_leads.deal_done_at',
                )
                ->orderBy('converted_leads.lead_id','desc')
                ->paginate(config('app.per_page'));


            return view('company_client.reports.lead_converted_detail', compact('leads', 'fromDate', 'toDate'));
        } catch (\Exception $e) {
            Log::error('API Lead Converted Detail Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Internal server error.'], 500);
        }
    }
}
