<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LeadServiceApiController extends Controller
{
    public function lead_service_list(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

            $services = Service::orderBy('service_id', 'desc')
                ->where('company_id', $employee->company_id)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Services fetched successfully',
                'data' => $services,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch services',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function lead_service_create(Request $request)
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
                'service_name' => 'required|string|max:255',
                'service_description' => 'required|string|max:255',
                'service_amount' => 'required',
            ]);

            $service = Service::create([
                'service_name' => $request->service_name,
                'service_description' => $request->service_description,
                'rate' => $request->service_amount,
                'company_id' => $employee->company_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Service created successfully',
                'data' => $service,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function lead_service_update(Request $request)
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
                'service_id' => 'required|integer|exists:service_master,service_id',
                'service_name' => 'required|string|max:255',
                'service_amount' => 'required',
                'service_description' => 'required|string|max:255',
            ]);

            $serviceupdate = Service::find($request->service_id);
            $serviceupdate->update([
                'service_name' => $request->service_name,
                'service_description' => $request->service_description,
                'rate' => $request->service_amount,
                'company_id' => $employee->company_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully',
                'data' => $serviceupdate,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function lead_service_delete(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

            $request->validate([
                'service_id' => 'required|integer|exists:service_master,service_id',
            ]);

            $service = Service::find($request->service_id);
            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
