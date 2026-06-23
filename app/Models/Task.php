<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'status',
        'assigned_employee_id',
        'created_by_employee_id',
        'due_date',
    ];

    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id', 'emp_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id', 'emp_id');
    }
}
