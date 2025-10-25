<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeadCancelReason;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LeadCancelReasonApiController extends Controller
{
    public function lead_cancel_reason_list(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

            $leadCancelReason = LeadCancelReason::orderBy('lead_cancel_reason_id', 'desc')->where(['company_id' =>$employee->company_id])->get();

            return response()->json([
                'success' => true,
                'message' => 'Cancel reason fetched successfully',
                'data' => $leadCancelReason,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch Cancel reason',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function lead_cancel_reason_create(Request $request)
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
                'reason' => 'required|string|max:255',
            ]);

            $data = $request->only('reason');
            $data['company_id'] = $employee->company_id;
            $leadCancelReason = LeadCancelReason::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Cancel reason created successfully',
                'data' => $leadCancelReason,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create cancel reason',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function lead_cancel_reason_edit(Request $request)
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
                'lead_cancel_reason_id' => 'required|integer|exists:lead_cancel_reason,lead_cancel_reason_id',
            ]);

            $reason = LeadCancelReason::find($request->lead_cancel_reason_id);

            return response()->json([
                'success' => true,
                'message' => 'Cancel reason fetched successfully',
                'data' => $reason,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cancel reason',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function lead_cancel_reason_update(Request $request)
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
                'lead_cancel_reason_id' => 'required|integer|exists:lead_cancel_reason,lead_cancel_reason_id',
                'reason' => 'required|string|max:255',
            ]);

            $reason = LeadCancelReason::find($request->lead_cancel_reason_id);
            $reason->update([
                'reason' => $request->reason,
                'company_id' => $employee->company_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cancel reason updated successfully',
                'data' => $reason,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cancel reason',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function lead_cancel_reason_delete(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

            $request->validate([
                'lead_cancel_reason_id' => 'required|integer|exists:lead_cancel_reason,lead_cancel_reason_id',
            ]);

            $reason = LeadCancelReason::find($request->lead_cancel_reason_id);
            $reason->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cancel reason deleted successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete cancel reason',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
