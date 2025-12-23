<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UdfMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UDFMasterApiController extends Controller
{

    public function udf_list(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

            $udfData = UdfMaster::where('company_id', $employee->company_id)
                ->where('isDelete', 0)
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'UDF list fetched successfully',
                'data' => $udfData,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch UDF list',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function udf_create(Request $request)
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
                'label' => 'required|string|max:255',
                'required' => 'required',
            ]);

            $udfData = UdfMaster::create([
                'label' => $request->label,
                'required' => $request->required,
                'company_id' => $employee->company_id,
                'iStatus' => 1,
                'isDelete' => 0,
                'strIP' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'UDF created successfully',
                'data' => $udfData,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create UDF',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function udf_update(Request $request)
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
                'id' => 'required|exists:udf_masters,id',
                'label' => 'required|string|max:255',
                'required' => 'required',
            ]);

            $udf = UdfMaster::where('company_id', $employee->company_id)
                ->find($request->id);

            if (!$udf) {
                return response()->json(['success' => false, 'message' => 'UDF not found'], 404);
            }

            $udf->update([
                'label' => $request->label,
                'required' => $request->required,
                'strIP' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'UDF updated successfully',
                'data' => $udf,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update UDF',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function udf_delete(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

            $request->validate([
                'id' => 'required|integer|exists:udf_masters,id',
            ]);

            $udf = UdfMaster::where('company_id', $employee->company_id)
                ->find($request->id);

            if (!$udf) {
                return response()->json(['success' => false, 'message' => 'UDF not found'], 404);
            }

            $udf->update(['isDelete' => 1]);

            return response()->json([
                'success' => true,
                'message' => 'UDF deleted successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete UDF',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
