@extends('layouts.client')
@section('title', 'Task Management')
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            @include('common.alert')
                        @if ($isCompanyAdmin)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Add Task</h5>
                    <form method="POST" action="{{ route('tasks.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label">Task</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Assign Employee</label>
                            <select name="assigned_employee_id" class="form-control" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->emp_id }}">{{ $employee->emp_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Description</label>
                            <input type="text" name="description" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Pending Tasks</h5>
                            @forelse($tasks->where('status', 'pending') as $task)
                                <div class="border rounded p-2 mb-2">
                                    <strong>{{ $task->title }}</strong><br>
                                    <small>Assigned: {{ $task->assignedEmployee->emp_name ?? 'Unassigned' }}</small><br>
                                    <small>Due: {{ $task->due_date ?? '-' }}</small><br>
                                    <small>Created by: {{ $task->createdBy->emp_name ?? '-' }}</small><br>
                                    @if ($task->description)
                                        <small>Description: {{ $task->description }}</small>
                                    @endif
                                    @if ($isCompanyAdmin)
                                    <div class="mt-2 d-flex gap-2">
                                        <form method="POST" action="{{ route('tasks.toggle', $task->id) }}">@csrf @method('PATCH')<button class="btn btn-success btn-sm">Mark Completed</button></form>
                                        <form method="POST" action="{{ route('tasks.delete', $task->id) }}">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Delete</button></form>
                                    </div>
                                                                        @endif
                                </div>
                            @empty
                                <p>No pending tasks.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Completed Tasks</h5>
                            @forelse($tasks->where('status', 'completed') as $task)
                                <div class="border rounded p-2 mb-2">
                                    <strong>{{ $task->title }}</strong><br>
                                    <small>Assigned: {{ $task->assignedEmployee->emp_name ?? 'Unassigned' }}</small><br>
                                    <small>Due: {{ $task->due_date ?? '-' }}</small><br>
                                    <small>Created by: {{ $task->createdBy->emp_name ?? '-' }}</small><br>
                                    @if ($task->description)
                                        <small>Description: {{ $task->description }}</small>
                                    @endif
                                    @if ($isCompanyAdmin)
                                    <div class="mt-2 d-flex gap-2">
                                        <form method="POST" action="{{ route('tasks.toggle', $task->id) }}">@csrf @method('PATCH')<button class="btn btn-warning btn-sm">Mark Pending</button></form>
                                        <form method="POST" action="{{ route('tasks.delete', $task->id) }}">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Delete</button></form>
                                    </div>
                                @endif
                                </div>
                            @empty
                                <p>No completed tasks.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
