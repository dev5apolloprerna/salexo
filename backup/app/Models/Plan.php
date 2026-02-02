<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    
    public $table = 'plan_master';
     protected $dates = [
        'created_at',
        'updated_at',
    ];
    protected $primaryKey = 'plan_id'; // Define the primary key
    protected $fillable = [
       'plan_id', 'plan_amount', 'plan_days', 'iStatus', 'isDelete', 'created_at', 'updated_at'
    ];
}
