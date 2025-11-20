<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeadMaster;
use App\Models\LeadPipeline;
use App\Models\Employee;
use App\Models\Service;
use App\Models\LeadSource;
use App\Models\UserData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WebHookController extends Controller
{
    public function web_hook(Request $request, $guid)
    {
        try {
            $employee = Employee::where('isDelete', 0)
                ->where('guid', $guid)
                ->first();
            $userData=UserData::where(['api_id'=>1])->first();
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid GUID or User not found'
                ], 404);
            }

            $request->validate([
                'SENDER_NAME' => 'required|string|max:255',
                'SENDER_MOBILE' => 'required',
                'QUERY_MESSAGE' => 'required|string',
                'QUERY_PRODUCT_NAME' => 'required'
            ]);

            $leadPipeline = LeadPipeline::where([
                'company_id' => $employee->company_id,
                'pipeline_name' => "New Lead"
            ])->first();

            $service = Service::where(['company_id' => $employee->company_id, 'service_name' => $request->QUERY_PRODUCT_NAME])->first();
            if ($service) {
                $service_name = $service->service_id;
            } else {
                $service_name = Service::create([
                    'company_id' => $employee->company_id,
                    'service_name' => $request->QUERY_PRODUCT_NAME,
                    'created_at' => now(),
                ]);
                $service_name = $service_name->service_id;
            }

            $lead_source = LeadSource::where(['company_id' => $employee->company_id, 'lead_source_name' => 'IndiaMart'])->first();
            if ($lead_source) {
                $lead_source = $lead_source->lead_source_id;
            } else {
                $lead_source = LeadSource::create([
                    'company_id' => $employee->company_id,
                    'lead_source_name' => 'IndiaMart',
                ]);
                $lead_source = $lead_source->lead_source_id;
            }

            $data = array(
                'iCustomerId' => $employee->company_id,
                'iemployeeId' => $userData->emp_id ?? $employee->emp_id ?? 0,
                'company_name' => $request->SENDER_COMPANY,
                'customer_name' => $request->SENDER_NAME,
                'email' => $request->SENDER_EMAIL,
                'mobile' => $request->SENDER_MOBILE,
                'address' => $request->SENDER_ADDRESS,
                'alternative_no' => $request->SENDER_MOBILE_ALT,
                'remarks' => $request->QUERY_MESSAGE,
                'product_service_id' => $service_name ?? 0,
                'LeadSourceId' => $lead_source ?? 0,
                'lead_history_id' => 0,
                'followup_by' => 0,
                'status' => $leadPipeline->pipeline_id ?? 0,
                'cancel_reason_id' => 0,
                'employee_id' => 0,
                'iEnterBy' => $employee->emp_id,
                'created_at' => now(),
            );
            $lead = LeadMaster::Create($data);


            return response()->json([
                'success' => true,
                'message' => 'Lead created successfully'
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

    public function crm_inquiry(Request $request, $guid)
    {
        try {
            $employee = Employee::where('isDelete', 0)
                ->where('guid', $guid)
                ->first();
            $userData=UserData::where(['api_id'=>1])->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid GUID or User not found'
                ], 404);
            }

            $request->validate([
                'contact_person_name' => 'required|string|max:255',
                'mobile' => 'required',
                'remarks' => 'required|string',
                'product_service' => 'required'
            ]);

            $leadPipeline = LeadPipeline::where([
                'company_id' => $employee->company_id,
                'pipeline_name' => "New Lead"
            ])->first();

            $service = Service::where(['company_id' => $employee->company_id, 'service_name' => $request->product_service])->first();
            if ($service) {
                $service_name = $service->service_id;
            } else {
                $service_name = Service::create([
                    'company_id' => $employee->company_id,
                    'service_name' => $request->product_service,
                    'created_at' => now(),
                ]);
                $service_name = $service_name->service_id;
            }

            $lead_source = LeadSource::where(['company_id' => $employee->company_id, 'lead_source_name' => $request->lead_source])->first();
            if ($lead_source) {
                $lead_source = $lead_source->lead_source_id;
            } else {
                $lead_source = LeadSource::create([
                    'company_id' => $employee->company_id,
                    'lead_source_name' => $request->lead_source,
                ]);
                $lead_source = $lead_source->lead_source_id;
            }

            $data = array(
                'iCustomerId' => $employee->company_id,
                'iemployeeId' => $userData->emp_id ?? $employee->emp_id,
                'company_name' => $request->company_name,
                'GST_No' => $request->gst_no,
                'customer_name' => $request->contact_person_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'alternative_no' => $request->alternative_mobile,
                'remarks' => $request->remarks,
                'product_service_id' => $service_name ?? 0,
                'LeadSourceId' => $lead_source ?? 0,
                'lead_history_id' => 0,
                'followup_by' => 0,
                'status' => $leadPipeline->pipeline_id ?? 0,
                'cancel_reason_id' => 0,
                'employee_id' => 0,
                'iEnterBy' => $employee->emp_id,
                'created_at' => now(),
            );
            $lead = LeadMaster::Create($data);


            return response()->json([
                'success' => true,
                'message' => 'Lead created successfully'
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
}
