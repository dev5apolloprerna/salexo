<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskManagementApiController extends Controller
{
    public function index(Request $request)
    {
        $employee = Auth::guard('employee_api')->user();
        $filters = $request->validate(['status' => 'nullable|in:pending,completed']);

        
        return response()->json([
            'success' => true,
            'message' => 'Task list fetched successfully',
'data' => $this->tasksFor($employee, $filters['status'] ?? null),
        ]);
    }

    public function pending()
    {
        $employee = Auth::guard('employee_api')->user();

        return response()->json([
            'success' => true,
            'message' => 'Pending task list fetched successfully',
            'data' => $this->tasksFor($employee, 'pending'),
        ]);
    }

    public function completed()
    {
        $employee = Auth::guard('employee_api')->user();

        return response()->json([
            'success' => true,
            'message' => 'Completed task list fetched successfully',
            'data' => $this->tasksFor($employee, 'completed'),
        ]);
    }

    public function store(Request $request)
    {
        $employee = Auth::guard('employee_api')->user();
        abort_unless($this->isCompanyAdmin($employee), 403, 'Only a company admin can create tasks.');

        $data = $this->validatedTask($request, $employee);
        $data['company_id'] = $employee->company_id;
        $data['created_by_employee_id'] = $employee->emp_id;

        $task = Task::create($data)->load(['assignedEmployee:emp_id,emp_name', 'createdBy:emp_id,emp_name']);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'data' => $task,
        ], 201);
    }

    public function update(Request $request, Task $task)
    {
        $employee = Auth::guard('employee_api')->user();
        $this->authorizeCompanyAdminTask($employee, $task);

        $task->update($this->validatedTask($request, $employee));

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'data' => $task->fresh()->load(['assignedEmployee:emp_id,emp_name', 'createdBy:emp_id,emp_name']),
        ]);
    }

    public function updateStatus(Request $request, Task $task)
    {
        $employee = Auth::guard('employee_api')->user();
        abort_unless($task->company_id == $employee->company_id, 403, 'This task belongs to another company.');

        $data = $request->validate(['status' => 'required|in:pending,completed']);
        $isAdmin = $this->isCompanyAdmin($employee);
        $isAssignedEmployee = $task->assigned_employee_id == $employee->emp_id;

        abort_unless(
            $isAdmin || ($isAssignedEmployee && $task->status === 'pending' && $data['status'] === 'completed'),
            403,
            'Employees can only complete their own pending tasks.'
        );

        $task->update(['status' => $data['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully',
            'data' => $task->fresh(),
        ]);
    }

    public function destroy(Task $task)
    {
        $employee = Auth::guard('employee_api')->user();
        $this->authorizeCompanyAdminTask($employee, $task);
        $task->delete();

        return response()->json(['success' => true, 'message' => 'Task deleted successfully']);
    }

    private function validatedTask(Request $request, Employee $employee): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_employee_id' => [
                'required',
                Rule::exists('employee_master', 'emp_id')->where(function ($query) use ($employee) {
                    return $query->where('company_id', $employee->company_id)->where('isDelete', 0);
                }),
            ],
            'status' => 'sometimes|in:pending,completed',
            'due_date' => 'nullable|date',
        ]);
    }

    private function authorizeCompanyAdminTask(Employee $employee, Task $task): void
    {
        abort_unless(
            $this->isCompanyAdmin($employee) && $task->company_id == $employee->company_id,
            403,
            'Only a company admin can manage this task.'
        );
    }

    private function isCompanyAdmin(Employee $employee): bool
    {
        return (int) $employee->role_id === 2 || (int) $employee->isCompanyAdmin === 1;
    }
     private function tasksFor(Employee $employee, ?string $status = null)
    {
        return Task::with(['assignedEmployee:emp_id,emp_name', 'createdBy:emp_id,emp_name'])
            ->where('company_id', $employee->company_id)
            ->when(!$this->isCompanyAdmin($employee), function ($query) use ($employee) {
                return $query->where('assigned_employee_id', $employee->emp_id);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->orderByRaw('due_date IS NULL, due_date ASC')
            ->latest('id')
            ->get();
    }
}
