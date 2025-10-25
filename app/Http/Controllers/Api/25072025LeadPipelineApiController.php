<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeadMaster;
use App\Models\LeadPipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LeadPipelineApiController extends Controller
{
    public function lead_pipeline_list(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }


            // $pipeline = LeadPipeline::select(

            //     'lead_pipeline_master.pipeline_id',
            //     'lead_pipeline_master.company_id',
            //     'lead_pipeline_master.pipeline_name',
            //     'lead_pipeline_master.slugname',
            //     'lead_pipeline_master.admin',
            //     'lead_pipeline_master.followup_needed',
            //     'lead_pipeline_master.followup_date',
            //     'lead_pipeline_master.created_at',
            //     'lead_pipeline_master.color',
            //     'lead_pipeline_master.icon',
            //     DB::raw('COUNT(lead_master.lead_id) as status_count')

            // )
            //     ->leftJoin('lead_master', function ($join) use ($employee) {
            //         $join->on('lead_master.status', '=', 'lead_pipeline_master.pipeline_id')
            //             ->where('lead_master.iemployeeId', $employee->company_id)
            //             ->where('lead_master.isDelete', 0);
            //     })
            //     ->where('lead_pipeline_master.company_id', $employee->company_id)
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
                'lead_pipeline_master.slugname',
                'lead_pipeline_master.admin',
                'lead_pipeline_master.followup_needed',
                'lead_pipeline_master.followup_date',
                'lead_pipeline_master.color',
                'lead_pipeline_master.icon',
                'lead_pipeline_master.created_at',
                'lead_pipeline_master.company_id',
                DB::raw('COUNT(lead_master.lead_id) as status_count')

            )
                ->leftJoin('lead_master', function ($join) use ($employee) {
                    $join->on('lead_master.status', '=', 'lead_pipeline_master.pipeline_id')
                        ->where('lead_master.iCustomerId', $employee->company_id)
                        ->where('lead_master.isDelete', 0);
                })
                ->where('lead_pipeline_master.company_id', $employee->company_id)
                ->whereNotIn('lead_pipeline_master.slugname', ['deal-done', 'deal-cancel'])
                ->groupBy(

                    'lead_pipeline_master.pipeline_id',
                    'lead_pipeline_master.pipeline_name',
                    'lead_pipeline_master.slugname',
                    'lead_pipeline_master.admin',
                    'lead_pipeline_master.followup_needed',
                    'lead_pipeline_master.followup_date',
                    'lead_pipeline_master.color',
                    'lead_pipeline_master.icon',
                    'lead_pipeline_master.created_at',
                    'lead_pipeline_master.company_id'

                );

            $piplineDones = LeadPipeline::select(

                'lead_pipeline_master.pipeline_id',
                'lead_pipeline_master.pipeline_name',
                'lead_pipeline_master.slugname',
                'lead_pipeline_master.admin',
                'lead_pipeline_master.followup_needed',
                'lead_pipeline_master.followup_date',
                'lead_pipeline_master.color',
                'lead_pipeline_master.icon',
                'lead_pipeline_master.created_at',
                'lead_pipeline_master.company_id',
                DB::raw('COUNT(deal_done.lead_id) as status_count')

            )
                ->leftJoin('deal_done', function ($join) use ($employee) {
                    $join->on('deal_done.status', '=', 'lead_pipeline_master.pipeline_id')
                        ->where('deal_done.iCustomerId', $employee->company_id)
                        ->where('deal_done.isDelete', 0);
                })
                ->where('lead_pipeline_master.company_id', $employee->company_id)
                ->whereIn('lead_pipeline_master.slugname', ['deal-done'])
                ->groupBy(

                    'lead_pipeline_master.pipeline_id',
                    'lead_pipeline_master.pipeline_name',
                    'lead_pipeline_master.slugname',
                    'lead_pipeline_master.admin',
                    'lead_pipeline_master.followup_needed',
                    'lead_pipeline_master.followup_date',
                    'lead_pipeline_master.color',
                    'lead_pipeline_master.icon',
                    'lead_pipeline_master.created_at',
                    'lead_pipeline_master.company_id'

                );

            $piplineCancels = LeadPipeline::select(

                'lead_pipeline_master.pipeline_id',
                'lead_pipeline_master.pipeline_name',
                'lead_pipeline_master.slugname',
                'lead_pipeline_master.admin',
                'lead_pipeline_master.followup_needed',
                'lead_pipeline_master.followup_date',
                'lead_pipeline_master.color',
                'lead_pipeline_master.icon',
                'lead_pipeline_master.created_at',
                'lead_pipeline_master.company_id',
                DB::raw('COUNT(deal_cancel.lead_id) as status_count')

            )
                ->leftJoin('deal_cancel', function ($join) use ($employee) {
                    $join->on('deal_cancel.status', '=', 'lead_pipeline_master.pipeline_id')
                        ->where('deal_cancel.iCustomerId', $employee->company_id)
                        ->where('deal_cancel.isDelete', 0);
                })
                ->where('lead_pipeline_master.company_id', $employee->company_id)
                ->whereIn('lead_pipeline_master.slugname', ['deal-cancel'])
                ->groupBy(

                    'lead_pipeline_master.pipeline_id',
                    'lead_pipeline_master.pipeline_name',
                    'lead_pipeline_master.slugname',
                    'lead_pipeline_master.admin',
                    'lead_pipeline_master.followup_needed',
                    'lead_pipeline_master.followup_date',
                    'lead_pipeline_master.color',
                    'lead_pipeline_master.icon',
                    'lead_pipeline_master.created_at',
                    'lead_pipeline_master.company_id'

                );

            $pipeline = $pipline->union($piplineDones)->union($piplineCancels)->get();

            //Today and Overdua Followup
            $allLeads = LeadMaster::where('iemployeeId', $employee->company_id)
                ->where('iStatus', 1)
                ->where('isDelete', 0)
                ->get();

            // Today's follow-ups
            $todays_followup = $allLeads->filter(function ($lead) {
                try {
                    if (!$lead->next_followup_date) return false;
                    $date = \Carbon\Carbon::createFromFormat('d-m-Y h:i A', trim($lead->next_followup_date));
                    return $date->isToday();
                } catch (\Exception $e) {
                    return false;
                }
            });

            // Overdue follow-ups
            $overdue_followup = $allLeads->filter(function ($lead) {
                try {
                    if (!$lead->next_followup_date) return false;
                    $date = \Carbon\Carbon::createFromFormat('d-m-Y h:i A', trim($lead->next_followup_date));
                    return $date->lt(today());
                } catch (\Exception $e) {
                    return false;
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Lead pipeline fetched successfully',
                'data' => $pipeline,
                'dashboard' => [
                    'followups_count'   => $todays_followup->count(),
                    'overdue_count'   => $overdue_followup->count(),
                ],

                // 'followups' => [
                //     'today' => [
                //         'count' => $todays_followup->count(),
                //         'leads' => $todays_followup->values(), // Optional: include lead data
                //     ],
                //     'overdue' => [
                //         'count' => $overdue_followup->count(),
                //         'leads' => $overdue_followup->values(), // Optional
                //     ]
                // ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch lead pipeline',
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    public function lead_pipeline_create(Request $request)
    {
        try {

            $employee = Auth::guard('employee_api')->user();
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 401);
            }

            $request->validate([
                'pipeline_name' => 'required',
                'followup_needed' => 'required',
                'color' => 'required|',
            ]);

            $data = $request->only([
                'pipeline_name',
                'followup_needed',
                'color'
            ]);
            $data['admin'] = 0;
            $data['company_id'] = $employee->company_id;
            $data['created_at'] = now();

            $pipeline = LeadPipeline::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Lead pipeline created successfully',
                'pipeline' => $pipeline
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create lead pipeline',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function lead_pipeline_edit(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 401);
            }

            $request->validate([
                'pipeline_id' => 'required|integer|exists:lead_pipeline_master,pipeline_id',
            ]);

            $pipeline = LeadPipeline::where('pipeline_id', $request->pipeline_id)->first();

            return response()->json([
                'success' => true,
                'message' => 'Pipeline fetched successfully',
                'data' => $pipeline,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pipeline',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function lead_pipeline_update(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 401);
            }

            $request->validate([
                'pipeline_id' => 'required|integer|exists:lead_pipeline_master,pipeline_id',
                'pipeline_name' => 'required|string|max:255',
                'followup_needed' => 'required',
                'color' => 'required|string|max:20',
            ]);

            $pipeline = LeadPipeline::find($request->pipeline_id);

            $pipeline->update([
                'pipeline_name' => $request->pipeline_name,
                'followup_needed' => $request->followup_needed,
                'company_id' => $employee->company_id,
                'color' => $request->color,
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pipeline updated successfully',
                'data' => $pipeline,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update pipeline',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function lead_pipeline_delete(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

            $request->validate([
                'pipeline_id' => 'required|integer|exists:lead_pipeline_master,pipeline_id',
            ]);

            $pipeline = LeadPipeline::find($request->pipeline_id);
            $pipeline->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pipeline deleted successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete pipeline',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
