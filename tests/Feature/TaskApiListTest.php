<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Task;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TaskApiListTest extends TestCase
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
            $table->boolean('isCompanyAdmin')->default(false);
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

    public function test_pending_list_only_returns_pending_tasks_assigned_to_employee(): void
    {
        [$employee, $otherEmployee] = $this->employees();
        $expected = $this->task($employee, 'pending', 'My pending task');
        $this->task($employee, 'completed', 'My completed task');
        $this->task($otherEmployee, 'pending', 'Another employee task');

        $response = $this->actingAs($employee, 'employee_api')
            ->postJson('http://localhost/api/employee/task/pending/list');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $expected->id)
            ->assertJsonPath('data.0.status', 'pending');
    }

    public function test_completed_list_only_returns_company_completed_tasks_for_admin(): void
    {
        [$employee, $admin] = $this->employees();
        $admin->update(['role_id' => 2, 'isCompanyAdmin' => true]);
        $expected = $this->task($employee, 'completed', 'Completed company task');
        $this->task($employee, 'pending', 'Pending company task');

        $outsideEmployee = Employee::create([
            'company_id' => 20,
            'role_id' => 3,
            'emp_name' => 'Outside Employee',
            'password' => bcrypt('password'),
        ]);
        $this->task($outsideEmployee, 'completed', 'Outside company task');

        $response = $this->actingAs($admin, 'employee_api')
            ->postJson('http://localhost/api/employee/task/completed/list');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $expected->id)
            ->assertJsonPath('data.0.status', 'completed');
    }

    private function employees(): array
    {
        return [
            Employee::create([
                'company_id' => 10,
                'role_id' => 3,
                'emp_name' => 'Assigned Employee',
                'password' => bcrypt('password'),
            ]),
            Employee::create([
                'company_id' => 10,
                'role_id' => 3,
                'emp_name' => 'Other Employee',
                'password' => bcrypt('password'),
            ]),
        ];
    }

    private function task(Employee $employee, string $status, string $title): Task
    {
        return Task::create([
            'company_id' => $employee->company_id,
            'title' => $title,
            'status' => $status,
            'assigned_employee_id' => $employee->emp_id,
            'created_by_employee_id' => $employee->emp_id,
        ]);
    }
}
