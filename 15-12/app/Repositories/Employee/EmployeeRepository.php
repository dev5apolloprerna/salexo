<?php

namespace App\Repositories\Employee;

use App\Models\Employee;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function all()
    {
        return Employee::where('isDelete', 0)->get();
    }

    public function find($id)
    {
        return Employee::findOrFail($id);
    }

    public function create(array $data)
    {
        return Employee::create($data);
    }

    public function update($id, array $data)
    {
        $employee = Employee::findOrFail($id);
        $employee->update($data);
        return $employee;
    }

    public function delete($id)
    {
        $employee = Employee::destroy($id);
        return true;
    }
    public function query()
    {
        return Employee::query();
    }
    public function updatePassword($id, $hashedPassword)
    {
        return Employee::where('emp_id', $id)->update([
            'password' => $hashedPassword,
        ]);
    }
}
