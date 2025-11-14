<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DealCancel;
use App\Models\DealDone;
use App\Models\Employee;
use App\Models\LeadCancelReason;
use App\Models\LeadHistory;
use App\Models\LeadMaster;
use App\Models\LeadPipeline;
use App\Models\LeadSource;
use App\Models\LeadUdfData;
use App\Models\CompanyClient;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\File;


class EmployeeApiController extends Controller
{
   public function login(Request $request)
{
    try {
        $request->validate([
            'mobileNumber' => 'required',
            'password'     => 'required',
        ]);

        $credentials = [
            'emp_mobile' => $request->mobileNumber,
            'password'   => $request->password,
        ];

        if (!Auth::guard('employee_api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $authEmployee = Auth::guard('employee_api')->user();
        $token        = JWTAuth::fromUser($authEmployee);

        // optional fields to update
        $authEmployee->update([
            'last_login'          => now(),
            'firebaseDeviceToken' => $request->firebaseDeviceToken,
        ]);

        // --- Company payload (payment detail + logo) ---
        $companyId = $authEmployee->company_id ?? $authEmployee->iCompanyId ?? null;

        $company = null;
        $companyPayload = null;
        $paymentDetail  = null;

        if ($companyId) {
            $company = DB::table('company_client_master')
                ->where('company_id', $companyId)
                ->select([
                    'company_id',
                    'company_name',
                    'delivery_terms',
                    'payment_terms',
                    'terms_condition',
                    'company_logo',
                    'Address',
                    'email',
                    'mobile',
                    'GST',
                    'plan_id',
                ])
                ->first();

            if ($company) {
                $companyLogoUrl = $this->makeAbsoluteLogoUrl($company->company_logo);


                // a concise block if you want it separately
                $paymentDetail = [
                    'GST'  => $company->GST,
                    'delivery_terms'  => $company->delivery_terms,
                    'payment_terms'   => $company->payment_terms,
                    'terms_condition' => $company->terms_condition,
                    'company_logo_url'=> $companyLogoUrl,
                ];
            }
        }

        return response()->json([
            'success'        => true,
            'message'        => 'Login successful',
            'employee'       => $authEmployee,     // your Employee model data
            'payment_detail' => $paymentDetail,    // null if not found
            'authorisation'  => [
                'token' => $token,
                'type'  => 'bearer',
            ],
        ], 200);

    } catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => 'Login failed',
            'error'   => $th->getMessage(),
        ], 500);
    }
}

    private function makeAbsoluteLogoUrl($path)
    {
        $path = trim((string) $path);
        if ($path === '') {
            return null;
        }

        // Already absolute?
        if (preg_match('~^https?://~i', $path)) {
            return $path;
        }

        // Common storage under /uploads/company/
        // If $path already starts with 'uploads/', don't double-prefix
        if (stripos($path, 'uploads/') === 0) {
            return asset($path);
        }

        return asset('uploads/company/' . ltrim($path, '/'));
    }


    public function firebase_device_update(Request $request)
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
                'firebaseDeviceToken' => 'required'
            ]);

            $employee->update([
                'firebaseDeviceToken' => $request->firebaseDeviceToken
            ]);

            return response()->json([
                'success' => true,
                'message' => "Firebase Device Token updated successfully"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update firebase device tolen',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function lead_pipeline(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 401);
            }

            $leadPipelines = LeadPipeline::where(['company_id' => $employee->company_id])->get();

            return response()->json([
                'success' => true,
                'message' => 'Lead pipelines fetched successfully',
                'data' => $leadPipelines,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch lead pipelines',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function lead_cancel_reason_list(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 401);
            }

            $reasons = LeadCancelReason::where(['company_id' => $employee->company_id])->get();

            return response()->json([
                'success' => true,
                'message' => 'Cancel reasons fetched successfully',
                'data' => $reasons,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cancel reasons',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function service_list(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 401);
            }

            $services = Service::where(['company_id' => $employee->company_id])->get();

            return response()->json([
                'success' => true,
                'message' => 'Service list fetched successfully',
                'data' => $services,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch service list',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function lead_source_list(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 401);
            }

            $leadSources = LeadSource::where(['company_id' => $employee->company_id])->get();

            return response()->json([
                'success' => true,
                'message' => 'Lead source list fetched successfully',
                'data' => $leadSources,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch lead source list',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function add_lead(Request $request)
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
                'iCustomerId' => 'required|exists:company_client_master,company_id',
                'company_name' => 'nullable|string|max:255',
                'GST_No' => 'nullable|string|max:255',
                'customer_name' => 'required|string|max:255',
                'email' => 'nullable|string|max:255',
                'mobile' => 'required|digits:10',
                'alternative_no' => 'nullable|digits:10',
                'address' => 'nullable|string',
                'remarks' => 'required|string',
                'product_service_id' => 'required|exists:service_master,service_id',
                'LeadSourceId' => 'required|exists:lead_source_master,lead_source_id',
                'employee_id' => 'required|exists:employee_master,emp_id',
                'initially_contacted' => 'required',
            ]);

            $data = $request->only([
                'iCustomerId',
                'company_name',
                'GST_No',
                'customer_name',
                'email',
                'mobile',
                'address',
                'alternative_no',
                'remarks',
                'product_service_id',
            ]);

            $data['LeadSourceId'] = $request->LeadSourceId ?? 0;
            $data['lead_history_id'] = $request->lead_history_id ?? 0;
            $data['iemployeeId'] = $employee->emp_id ?? 0;
            $data['created_at'] = now();
            $data['followup_by'] = 0;
            $data['next_followup_date'] = $request->followup_datetime;
            $data['status'] = $request->status ?? 0;
            $data['cancel_reason_id'] = $request->cancel_reason_id ?? 0;
            $data['amount'] = $request->amount ?? 0;
            $data['employee_id'] = $request->employee_id ?? 0;
            $data['initially_contacted'] = $request->initially_contacted ?? 0;
            $data['comments'] = $request->comment;
            $data['iEnterBy'] = $employee->emp_id;

            $lead = LeadMaster::create($data);

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

            $leadPipeline = LeadPipeline::where([
                'company_id' => $employee->company_id,
                'pipeline_name' => "New Lead"
            ])->first();

            if ($request->initially_contacted == "Yes") {
                $leadHistoryData = [
                    'iLeadId' => $lead->id ?? 0,
                    'iCustomerId' => $request->iCustomerId ?? 0,
                    'Comments' => $request->comment,
                    'followup_by' => 0,
                    'next_followup_date' => $request->followup_datetime,
                    'status' => $request->status ?? 0,
                    'cancel_reason_id' => $request->cancel_reason_id ?? 0,
                    'amount' => $request->amount ?? 0,
                    'created_at' => now(),
                    'iEnterBy' => $employee->emp_id,
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

            $new_lead_count = LeadMaster::where([
                'lead_master.iCustomerId' => $employee->emp_id,
                'lead_master.status' => $leadPipeline->pipeline_id
            ])
                ->where('lead_master.isDelete', 0)
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Lead created successfully',
                'lead' => $lead,
                'new_lead_count' => $new_lead_count,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create lead',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function lead_list(Request $request)
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
                'status' => 'required'
            ]);
            $companyId = $employee->company_id;

            $pipeline = LeadPipeline::where('pipeline_id', $request->status)->first();
            $status = $pipeline->slugname;

            if (!$pipeline) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid pipeline status provided.',
                ], 401);
            }

            $pipelineName = $pipeline->pipeline_name;

            if ($status === 'deal-done') {
                // Get leads from `deal_done` table
                $querys = DB::table('deal_done')
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
                    });

                // ✅ Add this if not admin
                if ($employee->isCompanyAdmin == 0) {
                    $querys->where('deal_done.iEnterBy', $employee->emp_id);
                };

                $leads =  $querys->leftJoin('service_master', 'deal_done.product_service_id', '=', 'service_master.service_id')
                    ->leftJoin('lead_source_master', 'deal_done.LeadSourceId', '=', 'lead_source_master.lead_source_id')
                    ->leftJoin('lead_cancel_reason', 'deal_done.cancel_reason_id', '=', 'lead_cancel_reason.lead_cancel_reason_id')
                    ->leftJoin('employee_master', 'deal_done.iemployeeId', '=', 'employee_master.emp_id')
                    ->select(
                        'deal_done.*',
                        'service_master.service_name',
                        'lead_cancel_reason.reason',
                        'lead_source_master.lead_source_name',
                        'employee_master.emp_name',
                    )
                    ->get();
            } elseif ($status === 'deal-cancel') {
                // Get leads from `deal_cancel` table
                $querys = DB::table('deal_cancel')
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
                    });

                // ✅ Add this if not admin
                if ($employee->isCompanyAdmin == 0) {
                    $querys->where('deal_cancel.iEnterBy', $employee->emp_id);
                };

                $leads =  $querys->leftJoin('service_master', 'deal_cancel.product_service_id', '=', 'service_master.service_id')
                    ->leftJoin('lead_source_master', 'deal_cancel.LeadSourceId', '=', 'lead_source_master.lead_source_id')
                    ->leftJoin('lead_cancel_reason', 'deal_cancel.cancel_reason_id', '=', 'lead_cancel_reason.lead_cancel_reason_id')
                    ->leftJoin('employee_master', 'deal_cancel.iemployeeId', '=', 'employee_master.emp_id')
                    ->select(
                        'deal_cancel.*',
                        'service_master.service_name',
                        'lead_cancel_reason.reason',
                        'lead_source_master.lead_source_name',
                        'employee_master.emp_name',
                    )
                    ->get();
            } else {
                // Get leads from `lead_master` table
                $querys = LeadMaster::where([
                    'lead_master.iStatus' => 1,
                    'lead_master.isDelete' => 0,
                    'lead_master.iCustomerId' => $companyId
                ])
                    ->whereIn('lead_master.status', function ($query) use ($companyId, $pipelineName) {
                        $query->select('pipeline_id')
                            ->from('lead_pipeline_master')
                            ->where('company_id', $companyId)
                            ->where('pipeline_name', 'like', $pipelineName);
                    });

                // ✅ Add this if not admin
                if ($employee->isCompanyAdmin == 0) {
                    $querys->where('lead_master.iEnterBy', $employee->emp_id);
                };

                $leads = $querys->leftJoin('service_master', 'lead_master.product_service_id', '=', 'service_master.service_id')
                    ->leftJoin('lead_source_master', 'lead_master.LeadSourceId', '=', 'lead_source_master.lead_source_id')
                    ->leftJoin('lead_cancel_reason', 'lead_master.cancel_reason_id', '=', 'lead_cancel_reason.lead_cancel_reason_id')
                    ->leftJoin('employee_master', 'lead_master.iemployeeId', '=', 'employee_master.emp_id')
                    ->select(
                        'lead_master.*',
                        'service_master.service_name',
                        'lead_cancel_reason.reason',
                        'lead_source_master.lead_source_name',
                        'employee_master.emp_name',
                    )
                    ->get();
            }

            // Example logic: fetch pending leads for this employee
            // $leads = LeadMaster::where('iemployeeId', $employee->emp_id)
            //     ->where('status', $request->status) // Adjust based on your DB
            //     ->orderBy('lead_id', 'desc')
            //     ->get();

            return response()->json([
                'success' => true,
                'message' => 'leads list fetched successfully',
                'data' => $leads,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch leads list',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function lead_detail(Request $request)
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
                'lead_id' => 'required'
            ]);

            $lead = LeadMaster::select(
                'lead_master.*',
                'lead_cancel_reason.reason',
                'service_master.service_name',
                'employee_master.emp_name',
            )
                ->leftJoin('lead_cancel_reason', 'lead_master.cancel_reason_id', '=', 'lead_cancel_reason.lead_cancel_reason_id')
                ->leftJoin('service_master', 'lead_master.product_service_id', '=', 'service_master.service_id')
                ->leftJoin('employee_master', 'lead_master.iemployeeId', '=', 'employee_master.emp_id')
                ->where('lead_master.iCustomerId', $employee->company_id)
                ->where('lead_master.lead_id', $request->lead_id)
                ->when($employee->isCompanyAdmin == 0, function ($query) use ($employee) {
                    $query->where('lead_master.iEnterBy', $employee->emp_id);
                })
                ->first();

            if (!$lead) {
                $lead = DealDone::select(
                    'deal_done.*',
                    'lead_cancel_reason.reason',
                    'service_master.service_name',
                    'employee_master.emp_name',
                )
                    ->leftJoin('lead_cancel_reason', 'deal_done.cancel_reason_id', '=', 'lead_cancel_reason.lead_cancel_reason_id')
                    ->leftJoin('service_master', 'deal_done.product_service_id', '=', 'service_master.service_id')
                    ->leftJoin('employee_master', 'deal_done.iemployeeId', '=', 'employee_master.emp_id')
                    ->where('deal_done.iCustomerId', $employee->company_id)
                    ->where('deal_done.lead_id', $request->lead_id)
                    ->when($employee->isCompanyAdmin == 0, function ($query) use ($employee) {
                        $query->where('deal_done.iEnterBy', $employee->emp_id);
                    })
                    ->first();
            }

            if (!$lead) {
                $lead = DealCancel::select(
                    'deal_cancel.*',
                    'lead_cancel_reason.reason',
                    'service_master.service_name',
                    'employee_master.emp_name',
                )
                    ->leftJoin('lead_cancel_reason', 'deal_cancel.cancel_reason_id', '=', 'lead_cancel_reason.lead_cancel_reason_id')
                    ->leftJoin('service_master', 'deal_cancel.product_service_id', '=', 'service_master.service_id')
                    ->leftJoin('employee_master', 'deal_cancel.iemployeeId', '=', 'employee_master.emp_id')
                    ->where('deal_cancel.iCustomerId', $employee->company_id)
                    ->where('deal_cancel.lead_id', $request->lead_id)
                    ->when($employee->isCompanyAdmin == 0, function ($query) use ($employee) {
                        $query->where('deal_cancel.iEnterBy', $employee->emp_id);
                    })
                    ->first();
            }

            if (!$lead) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lead not found for this user',
                ], 404);
            }

            $lead_history = DB::table('lead_history')
                ->select('lead_history.*', 'lead_pipeline_master.pipeline_name', 'lead_cancel_reason.reason')
                ->leftjoin('lead_cancel_reason', 'lead_history.cancel_reason_id', '=', 'lead_cancel_reason.lead_cancel_reason_id')
                ->leftjoin('lead_pipeline_master', 'lead_history.status', '=', 'lead_pipeline_master.pipeline_id')
                ->where('iLeadId', $request->lead_id)
                ->orderBy('created_at', 'desc')
                ->get();
            // dd($lead_history);

            $leadUdfData = LeadUdfData::select(
                'lead_udf_data.*',
                'udf_masters.label'
            )
                ->leftjoin('udf_masters', 'lead_udf_data.udf_id', '=', 'udf_masters.id')
                ->where(['lead_udf_data.lead_id' => $request->lead_id, 'lead_udf_data.isDelete' => 0])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Lead detail fetched successfully',
                'data' => [
                    'lead' => $lead,
                    'lead_history' => $lead_history,
                    'lead_Udf_Data' => $leadUdfData,
                ],

            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch lead detail',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function change_password(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ]);
            }

            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:6|confirmed',
                'new_password_confirmation' => 'required'
            ]);

            // Check if current password matches
            if (!Hash::check($request->current_password, $employee->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 400);
            }

            // Manual check for password confirmation
            if ($request->new_password !== $request->new_password_confirmation) {
                return response()->json([
                    'success' => false,
                    'message' => 'New password and confirmation do not match'
                ], 422);
            }

            // Update password
            $employee->password = Hash::make($request->new_password);
            $employee->save();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function followup_update(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $request->validate([
                'lead_id' => 'required',
                'status' => 'required',
                'cancel_reason_id' => 'nullable|exists:lead_cancel_reason,lead_cancel_reason_id',
                'followup_datetime' => 'nullable',
                'amount' => 'nullable|numeric',
                'comment' => 'required',
            ]);

            $lead = LeadMaster::findOrFail($request->lead_id);
            $leadHistoryData = [
                'iLeadId' => $request->lead_id ?? 0,
                'iCustomerId' => $lead->iCustomerId ?? 0,
                'Comments' => $request->comment,
                'followup_by' => $request->followup_by ?? $employee->emp_id,
                'next_followup_date' => $request->followup_datetime,
                'status' => $request->status ?? 0,
                'cancel_reason_id' => $request->cancel_reason_id ?? 0,
                'amount' => $request->amount ?? 0,
                'created_at' => now(),
            ];
            $leadHistory = LeadHistory::create($leadHistoryData);

            $lead->update([
                'lead_history_id' => $leadHistory->id,
                'comments' => $request->comment,
                'followup_by' => $request->followup_by ?? $employee->emp_id,
                'next_followup_date' => $request->followup_datetime,
                'status' => $request->status,
                'cancel_reason_id' => $request->cancel_reason_id ?? 0,
                'amount' => $request->amount ?? 0,
            ]);

            $lead_pipeline = LeadPipeline::where([
                'company_id' => $employee->company_id,
                'pipeline_id' => $request->status
            ])->first();

            if ($lead_pipeline && $lead_pipeline->slugname === "deal-done") {

                $dealDoneData = $lead->toArray();
                $dealDoneData['deal_done_at'] = now();
                DealDone::create($dealDoneData);
                $lead->delete();
            } else if ($lead_pipeline && $lead_pipeline->slugname === "deal-cancel") {

                DealCancel::create($lead->toArray());
                $lead->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Follow-up updated successfully.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update follow-up',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function forgot_password(Request $request)
    {
        try {

            $request->validate([
                'mobile' => 'required'
            ]);

            $employee = Employee::where(['emp_mobile' => $request->mobile])->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found with this mobile number'
                ], 404);
            }

            $otp = mt_rand(1000, 9999);
            $expiry_date = now()->addMinutes(3);

            $employee->otp = $otp;
            $employee->otp_expire_time = $expiry_date;
            $employee->save();

            // WhatsApp API credentials
            $whatsappToken = config('app.whatsapp_token');
            $phoneNumberId = config('app.whatsapp_phone_id');
            $recipient = '+91' . $employee->emp_mobile;
            // $recipient = '+919725123569';

            $response = Http::withToken($whatsappToken)->post("https://graph.facebook.com/v19.0/{$phoneNumberId}/messages", [
                "messaging_product" => "whatsapp",
                "to" => $recipient,
                "type" => "template",
                "template" => [
                    "name" => "forgot_password",
                    "language" => [
                        "code" => "en"
                    ],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $otp
                                ]
                            ]
                        ],
                        [
                            "type" => "button",
                            "sub_type" => "url",
                            "index" => "0",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $otp
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            if (!$response->successful()) {
                Log::error('WhatsApp API Error', [
                    'status' => $response->status(),
                    'body' => $response->json()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP via WhatsApp',
                    'error' => $response->json()
                ], 500);
            }


            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your registered mobile number',
                // 'otp' => $otp
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function otp_verify(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10',
                'otp' => 'required|digits:4'
            ]);

            $employee = Employee::where('emp_mobile', $request->mobile)->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found with this mobile number'
                ], 404);
            }

            if ($employee->otp != $request->otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP'
                ], 400);
            }

            if (now()->gt($employee->otp_expire_time)) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired'
                ], 400);
            }

            $employee->otp = 0;
            $employee->otp_expire_time = null;
            $employee->save();

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify OTP',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function set_new_password(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10',
                'new_password' => 'required|min:6|confirmed',
                'new_password_confirmation' => 'required'
            ]);

            $employee = Employee::where('emp_mobile', $request->mobile)->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found with this mobile number'
                ], 404);
            }

            if ($employee->otp != $request->otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP'
                ], 400);
            }

            $employee->password = Hash::make($request->new_password);
            $employee->save();

            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    
   public function profile_detail(Request $request)
{
    try {
        $employee = Auth::guard('employee_api')->user();
        if (!$employee) {
            return response()->json(['success'=>false,'message'=>'Unauthorized access'], 401);
        }

        $user = Employee::from('employee_master as e')
            ->leftJoin('company_client_master as c', 'c.company_id', '=', 'e.company_id')
            ->where('e.emp_id', $employee->emp_id)
            ->select([
                'e.*',
                'c.payment_terms',
                'c.delivery_terms',
                'c.terms_condition',
                'c.company_logo',
                'c.company_name',
                'c.Address',
                'c.email as company_email',
                'c.mobile as company_mobile',
                'c.GST as company_gst',
                'c.plan_id as company_plan_id',
            ])
            ->first();

        if (!$user) {
            return response()->json(['success'=>false,'message'=>'Employee not found'], 404);
        }

        // === SIMPLE logo handling (same as web) ===
        $root = rtrim(base_path('../public_html/'), '/').'/';
        $val  = trim((string) $user->company_logo);

        $companyLogoUrl  = null; // asset/http url
        $companyLogoData = null; // data:image/...;base64,...

        if ($val !== '' && preg_match('~^https?://~i', $val)) {
            // Already absolute URL -> use it; data-URI from fallback
            $companyLogoUrl = $val;
            $path = $root . 'assets/images/favicon.png';
        } else {
            // Relative path: prefix if needed and fallback
            $rel = $val === '' ? 'assets/images/favicon.png'
                 : (str_contains($val, '/') ? $val : 'uploads/company/'.$val);

            $abs = $root . ltrim($rel, '/');
            if (!is_file($abs)) {
                $rel = 'assets/images/favicon.png';
                $abs = $root . $rel;
            }

            $companyLogoUrl = asset($rel);
            $path = $abs;
        }

        // Make data-URI (best-effort)
        if (is_file($path)) {
            $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION) ?: 'png');
            $mime = match ($ext) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png'         => 'image/png',
                'gif'         => 'image/gif',
                'webp'        => 'image/webp',
                'svg'         => 'image/svg+xml',
                default       => 'image/png',
            };
            $bin = @file_get_contents($path);
            if ($bin !== false) {
                $companyLogoData = 'data:'.$mime.';base64,'.base64_encode($bin);
            }
        }

        // attach to payload
        $user->company_logo_url      = $companyLogoUrl;
        $user->company_logo_data_uri = $companyLogoData;

        return response()->json([
            'success' => true,
            'message' => 'Employee profile fetched successfully',
            'data'    => $user,
        ]);
    } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch profile detail',
            'error'   => $th->getMessage(),
        ], 500);
    }
}



     /*public function profile_update(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            $company_id = Auth::guard('employee_api')->user()->company_id;

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 401);
            }

            $validated = $request->validate([
                'emp_name' => 'required|string|max:255',
                'emp_email' => 'required|email'
            ]);

            $employee->update($validated);

            CompanyClient::where(['company_id' => $company_id])->update([
                    'payment_terms'=>$request->payment_terms,
                    'delivery_terms'=>$request->delivery_terms,
                    'terms_condition'=>$request->terms_condition,

                ]);


            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data'    => $employee,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }*/
     public function profile_update(Request $request)
{
    try {
        $employee = Auth::guard('employee_api')->user();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 401);
        }

        $company_id = $employee->company_id;

        // ✅ Validate
        $validated = $request->validate([
            'emp_name'   => 'required|string|max:255',
            'emp_email'  => 'nullable|email|unique:employee_master,emp_email,' . $employee->emp_id . ',emp_id',
            'emp_mobile' => 'nullable|numeric|digits:10',

            // ✅ logo optional
            'company_logo' => 'nullable|image|mimes:png,jpg,jpeg,webp,gif|max:3072',
        ]);

        DB::beginTransaction();

        // ✅ Update employee basic fields
        $employee->update($validated);

        // ✅ Prepare company update
        $updateCompany = [
            'contact_person_name'=> $request->emp_name,
            'payment_terms'   => $request->payment_terms,
            'delivery_terms'  => $request->delivery_terms,
            'terms_condition' => $request->terms_condition,
        ];

        // ✅ Get current company info (for old logo delete)
        $company = CompanyClient::where('company_id', $company_id)->first();

        // ✅ Handle company logo upload
        if ($request->hasFile('company_logo')) {
            $file = $request->file('company_logo');

            // folder: public_html/uploads/company
            $destAbs = public_path('../uploads/company');
            if (!File::isDirectory($destAbs)) {
                File::makeDirectory($destAbs, 0775, true);
            }

            $ext   = strtolower($file->getClientOriginalExtension());
            $fname = 'company_' . (int)$company_id . '_' . date('Ymd_His') . '.' . $ext;

            $file->move($destAbs, $fname);
            $newRelPath = 'uploads/company/' . $fname;  // saved path

            // ✅ Delete old logo if exists
            if (!empty($company->company_logo)) {
                $oldAbs = public_path('../' . ltrim($company->company_logo, '/\\'));
                if (File::exists($oldAbs)) {
                    @File::delete($oldAbs);
                }
            }

            // ✅ Update new logo in DB
            $updateCompany['company_logo'] = $newRelPath;
        }

        // ✅ Commit company update
        CompanyClient::where('company_id', $company_id)->update($updateCompany);

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
                
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'employee' => $employee,
            'company'  => CompanyClient::find($company_id),
        ], 200);

    } catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    } catch (\Throwable $th) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Failed to update profile',
            'error'   => $th->getMessage(),
        ], 500);
    }
}

    public function employee_list(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();

            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

            $employees = Employee::where([
                'isDelete' => 0,
                'company_id' => $employee->company_id
            ])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'User list fetched successfully',
                'data' => $employees,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user list',
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    // dashboard todays_followup_list
    public function todays_followup_list(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();

            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

            $query = LeadMaster::where([
                'lead_master.iStatus' => 1,
                'lead_master.isDelete' => 0,
                'lead_master.iCustomerId' => $employee->company_id,
                // 'lead_master.iemployeeId' => $employee->emp_id,
            ]);

            // ✅ Add this if not admin
            if ($employee->isCompanyAdmin == 0) {
                $query->where('lead_master.iEnterBy', $employee->emp_id);
            }

            $leads =  $query->leftjoin('service_master', 'lead_master.product_service_id', '=', 'service_master.service_id')
                ->leftjoin('lead_source_master', 'lead_master.LeadSourceId', '=', 'lead_source_master.lead_source_id')
                ->leftjoin('employee_master', 'lead_master.iemployeeId', '=', 'employee_master.emp_id')
                ->select(
                    'lead_master.*',
                    'service_master.service_name',
                    'lead_source_master.lead_source_name',
                    'employee_master.emp_name',
                )
                ->get();
            // dd($leads);

            $todaysFollowups = $leads->filter(function ($lead) {
                try {
                    $date = Carbon::createFromFormat('d-m-Y h:i A', trim($lead->next_followup_date));
                    return $date->isToday();
                } catch (\Exception $e) {
                    Log::error('Date parsing error in todays_followup_list: ' . $e->getMessage(), [
                        'lead_id' => $lead->lead_id ?? null
                    ]);
                    return false;
                }
            })->values();

            return response()->json([
                'success' => true,
                'message' => 'Todays follow-ups fetched successfully.',
                'data' => $todaysFollowups
            ]);
        } catch (\Exception $e) {
            Log::error('Error in apiTodaysFollowupList: ' . $e->getMessage(), [
                'employee_id' => $employee->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    // dashboard over_due_followup
    public function over_due_followup_list(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();

            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

            $query = LeadMaster::where([
                'lead_master.iStatus' => 1,
                'lead_master.isDelete' => 0,
                'lead_master.iCustomerId' => $employee->company_id
            ]);

            // ✅ Add this if not admin
            if ($employee->isCompanyAdmin == 0) {
                $query->where('lead_master.iEnterBy', $employee->emp_id);
            }

            $leads = $query->leftjoin('service_master', 'lead_master.product_service_id', '=', 'service_master.service_id')
                ->leftjoin('lead_source_master', 'lead_master.LeadSourceId', '=', 'lead_source_master.lead_source_id')
                ->leftjoin('employee_master', 'lead_master.iemployeeId', '=', 'employee_master.emp_id')
                ->select(
                    'lead_master.*',
                    'service_master.service_name',
                    'lead_source_master.lead_source_name',
                    'employee_master.emp_name',
                )
                ->get();

            $overdueFollowups = $leads->filter(function ($lead) {
                try {
                    $date = Carbon::createFromFormat('d-m-Y h:i A', trim($lead->next_followup_date));
                    return $date->lt(today());
                } catch (\Exception $e) {
                    Log::error('Date parsing error in over_due_followup: ' . $e->getMessage(), [
                        'lead_id' => $lead->lead_id ?? null
                    ]);
                    return false;
                }
            })->values(); // ensures array output format

            return response()->json([
                'success' => true,
                'message' => 'Overdue follow-ups fetched successfully.',
                'data' => $overdueFollowups
            ]);
        } catch (\Exception $e) {
            Log::error('Error in over_due_followup: ' . $e->getMessage(), [
                'employee_id' => $employee->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function lead_active(Request $request)
    {
        try {

            $employee = Auth::guard('employee_api')->user();

            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

            $query = LeadMaster::select(
                'lead_master.*',
                'service_master.service_name',
                'lead_source_master.lead_source_name',
                'lead_cancel_reason.reason',
            )
                ->leftjoin('lead_cancel_reason', 'lead_master.cancel_reason_id', '=', 'lead_cancel_reason.lead_cancel_reason_id')
                ->leftjoin('lead_source_master', 'lead_master.LeadSourceId', '=', 'lead_source_master.lead_source_id')
                ->leftjoin('service_master', 'lead_master.product_service_id', '=', 'service_master.service_id')
                ->where([
                    'lead_master.isDelete' => 0,
                    'lead_master.iCustomerId' => $employee->company_id
                ]);

            // ✅ Add this if not admin
            if ($employee->isCompanyAdmin == 0) {
                $query->where('lead_master.iEnterBy', $employee->emp_id);
            }

            if ($request->from_date) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->to_date) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            $leads = $query->get();

            return response()->json([
                'success' => true,
                'data' => $leads,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in leads_active: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function lead_done(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            $fromDateFormatted = $fromDate ? Carbon::createFromFormat('d-m-Y', $fromDate)->startOfDay() : null;
            $toDateFormatted = $toDate ? Carbon::createFromFormat('d-m-Y', $toDate)->endOfDay() : null;

            $query = DealDone::select(
                'deal_done.*',
                'service_master.service_name',
                'lead_source_master.lead_source_name',
                'lead_cancel_reason.reason',
            )
                ->leftjoin('lead_cancel_reason', 'deal_done.cancel_reason_id', '=', 'lead_cancel_reason.lead_cancel_reason_id')
                ->leftjoin('lead_source_master', 'deal_done.LeadSourceId', '=', 'lead_source_master.lead_source_id')
                ->leftjoin('service_master', 'deal_done.product_service_id', '=', 'service_master.service_id')
                ->where([
                    'deal_done.isDelete' => 0,
                    'deal_done.iCustomerId' => $employee->company_id
                ]);

            // ✅ Add this if not admin
            if ($employee->isCompanyAdmin == 0) {
                $query->where('deal_done.iEnterBy', $employee->emp_id);
            }

            if ($fromDateFormatted) {
                $query->where('deal_done.created_at', '>=', $fromDateFormatted);
            }

            if ($toDateFormatted) {
                $query->where('deal_done.created_at', '<=', $toDateFormatted);
            }

            $leads = $query->get();

            return response()->json([
                'success' => true,
                'data' => $leads,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in leads_done: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function lead_cancel(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            $fromDateFormatted = $fromDate ? Carbon::createFromFormat('d-m-Y', $fromDate)->startOfDay() : null;
            $toDateFormatted = $toDate ? Carbon::createFromFormat('d-m-Y', $toDate)->endOfDay() : null;

            $query = DealCancel::select(
                'deal_cancel.*',
                'service_master.service_name',
                'lead_source_master.lead_source_name',
                'lead_cancel_reason.reason',
            )
                ->leftjoin('lead_cancel_reason', 'deal_cancel.cancel_reason_id', '=', 'lead_cancel_reason.lead_cancel_reason_id')
                ->leftjoin('lead_source_master', 'deal_cancel.LeadSourceId', '=', 'lead_source_master.lead_source_id')
                ->leftjoin('service_master', 'deal_cancel.product_service_id', '=', 'service_master.service_id')
                ->where([
                    'deal_cancel.isDelete' => 0,
                    'deal_cancel.iCustomerId' => $employee->company_id
                ]);

            // ✅ Add this if not admin
            if ($employee->isCompanyAdmin == 0) {
                $query->where('deal_cancel.iEnterBy', $employee->emp_id);
            }

            if ($fromDateFormatted) {
                $query->where('deal_cancel.created_at', '>=', $fromDateFormatted);
            }

            if ($toDateFormatted) {
                $query->where('deal_cancel.created_at', '<=', $toDateFormatted);
            }

            $leads = $query->get();

            return response()->json([
                'success' => true,
                'data' => $leads,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in leads_cancel: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
}
