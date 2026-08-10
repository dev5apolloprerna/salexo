<?php

namespace Tests\Feature;

use App\Http\Controllers\TaskManagementController;
use App\Models\Employee;
use App\Models\Task;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class EmployeeTaskStatusTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');

        Schema::create('employee_master', function (Blueprint $table) {
            $table->increments('emp_id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('role_id');
            $table->string('emp_name');
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedInteger('assigned_employee_id');
            $table->unsignedInteger('created_by_employee_id')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    public function test_employee_can_complete_an_assigned_pending_task(): void
    {
        [$employee, $task] = $this->makeEmployeeAndTask();

        $this->actingAs($employee, 'web_employees');
        $response = app(TaskManagementController::class)->updateStatus($task);

        $this->assertSame(route('task.management'), $response->getTargetUrl());
        $this->assertSame('completed', $task->fresh()->status);
    }

    public function test_employee_cannot_update_a_task_assigned_to_someone_else(): void
    {
        [$employee, $task] = $this->makeEmployeeAndTask(['assigned_employee_id' => 999]);

        $this->actingAs($employee, 'web_employees');

        try {
            app(TaskManagementController::class)->updateStatus($task);
            $this->fail('An employee updated a task assigned to someone else.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }

        $this->assertSame('pending', $task->fresh()->status);
    }

    public function test_employee_cannot_move_a_completed_task_back_to_pending(): void
    {
        [$employee, $task] = $this->makeEmployeeAndTask(['status' => 'completed']);

        $this->actingAs($employee, 'web_employees');

        try {
            app(TaskManagementController::class)->updateStatus($task);
            $this->fail('An employee moved a completed task back to pending.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }

        $this->assertSame('completed', $task->fresh()->status);
    }

    private function makeEmployeeAndTask(array $taskAttributes = []): array
    {
        $employee = Employee::create([
            'company_id' => 10,
            'role_id' => 3,
            'emp_name' => 'Assigned Employee',
            'password' => bcrypt('password'),
        ]);

        $task = Task::create(array_merge([
            'company_id' => 10,
            'title' => 'Follow up with customer',
            'status' => 'pending',
            'assigned_employee_id' => $employee->emp_id,
            'created_by_employee_id' => $employee->emp_id,
        ], $taskAttributes));

        return [$employee, $task];
    }
}
