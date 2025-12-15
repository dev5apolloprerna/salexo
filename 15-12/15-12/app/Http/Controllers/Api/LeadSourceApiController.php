<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeadCancelReason;
use App\Models\LeadSource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LeadSourceApiController extends Controller
{
    public function lead_source_list(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

            $sources = LeadSource::orderBy('lead_source_id', 'desc')->where(['company_id' =>$employee->company_id])->get();

            return response()->json([
                'success' => true,
                'message' => 'Lead sources fetched successfully',
                'data' => $sources,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch lead sources',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function lead_source_create(Request $request)
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
                'lead_source_name' => 'required|string|max:255',
            ]);

            $source = LeadSource::create([
                'lead_source_name' => $request->lead_source_name,
                'company_id' => $employee->company_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead source created successfully',
                'data' => $source,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create lead source',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function lead_source_edit(Request $request)
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
                'lead_source_id' => 'required|integer|exists:lead_source_master,lead_source_id',
            ]);

            $source = LeadSource::find($request->lead_source_id);

            return response()->json([
                'success' => true,
                'message' => 'Lead source fetched successfully',
                'data' => $source,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch lead source',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function lead_source_update(Request $request)
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
                'lead_source_id' => 'required|integer|exists:lead_source_master,lead_source_id',
                'lead_source_name' => 'required|string|max:255',
            ]);

            $source  = LeadSource::find($request->lead_source_id);
            $source->update([
                'lead_source_name' => $request->lead_source_name,
                'company_id' => $employee->company_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead source updated successfully',
                'data' => $source,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update lead source',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function lead_source_delete(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

            $request->validate([
                'lead_source_id' => 'required|integer|exists:lead_source_master,lead_source_id',
            ]);

            $source  = LeadSource::find($request->lead_source_id);
            $source->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lead source deleted successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete lead source',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
