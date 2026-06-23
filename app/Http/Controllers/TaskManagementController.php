<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskManagementController extends Controller
{
    public function index()
    {
        $user = Auth::guard('web_employees')->user();

        $employees = Employee::where('company_id', $user->company_id)
            ->where('isDelete', 0)
            ->orderBy('emp_name')
            ->get(['emp_id', 'emp_name']);

        $tasksQuery = Task::with(['assignedEmployee', 'createdBy'])
            ->where('company_id', $user->company_id);

        if ((int) $user->role_id !== 2) {
            $tasksQuery->where(function ($query) use ($user) {
                $query->where('assigned_employee_id', $user->emp_id)
                    ->orWhere('created_by_employee_id', $user->emp_id);
            });
        }

        $tasks = $tasksQuery->latest()->get();

        return view('company_client.tasks.index', compact('employees', 'tasks'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('web_employees')->user();

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_employee_id' => 'nullable|exists:employee_master,emp_id',
            'status' => 'required|in:pending,completed',
            'due_date' => 'nullable|date',
        ]);

        $data['company_id'] = $user->company_id;
        $data['created_by_employee_id'] = $user->emp_id;

        Task::create($data);

        return redirect()->route('task.management')->with('success', 'Task created successfully.');
    }

    public function updateStatus(Task $task)
    {
        $user = Auth::guard('web_employees')->user();
        abort_unless($task->company_id == $user->company_id, 403);

        $task->status = $task->status === 'pending' ? 'completed' : 'pending';
        $task->save();

        return redirect()->route('task.management')->with('success', 'Task status updated.');
    }

    public function destroy(Task $task)
    {
        $user = Auth::guard('web_employees')->user();
        abort_unless($task->company_id == $user->company_id, 403);

        $task->delete();

        return redirect()->route('task.management')->with('success', 'Task deleted successfully.');
    }
}
