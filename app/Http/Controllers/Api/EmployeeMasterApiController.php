<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyClient;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EmployeeMasterApiController extends Controller
{
    public function employee_list(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

            $company_client = CompanyClient::where(['company_id' => $employee->company_id])->first();
            $currentEmployeeCount = Employee::where('company_id', $employee->company_id)
                ->where('isDelete', 0)
                ->count();

            $employees = Employee::where([
                'isDelete' => 0,
                // 'isCompanyAdmin' => 0,
                'company_id' => $employee->company_id
            ])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'User list fetched successfully',
                'data' => $employees,
                'total_count_user' => $company_client->no_of_users,
                'current_user_count' => $currentEmployeeCount,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user list',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function employee_create(Request $request)
    {
        try {

            $employee = Auth::guard('employee_api')->user();
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 401);
            }

            $company_client = CompanyClient::where(['company_id' => $employee->company_id])->first();

            $currentEmployeeCount = Employee::where('company_id', $employee->company_id)
                ->where('isDelete', 0)
                ->count();

            // Check if the employee limit is reached
            if ($currentEmployeeCount >= $company_client->no_of_users) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have reached the maximum number of users allowed for your plan.',
                ], 403);
            }

            $request->validate([
                'emp_name' => 'required',
                'emp_mobile' => 'required|unique:employee_master,emp_mobile',
                'emp_email' => 'nullable|email',
                'password' => 'required',
                'role_id' => 'required',
            ]);

            $guid = Str::uuid();
            $newEmployee = Employee::create([
                'guid' => $guid,
                'emp_name' => $request->emp_name,
                'emp_mobile' => $request->emp_mobile,
                'emp_email' => $request->emp_email,
                'password' => Hash::make($request->password),
                'company_id' => $employee->company_id,
                'role_id' => $request->role_id,
                'isCompanyAdmin' => $request->role_id == 2 ? 1 : 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $newEmployee,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function employee_update(Request $request)
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
                'emp_id' => 'required|exists:employee_master,emp_id',
                'emp_name' => 'required',
                'emp_mobile' => 'required|unique:employee_master,emp_mobile,' . $request->emp_id . ',emp_id',
                'emp_email' => 'nullable|email',
                'role_id' => 'required',
            ]);

            $targetEmployee = Employee::find($request->emp_id);
            $targetEmployee->update([
                'emp_name' => $request->emp_name,
                'emp_mobile' => $request->emp_mobile,
                'emp_email' => $request->emp_email,
                'role_id' => $request->role_id,
                'isCompanyAdmin' => $request->role_id == 2 ? 1 : 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $targetEmployee,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function employee_delete(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

            $request->validate([
                'emp_id' => 'required|integer|exists:employee_master,emp_id',
            ]);

            $targetEmployee = Employee::find($request->emp_id);
            $targetEmployee->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
