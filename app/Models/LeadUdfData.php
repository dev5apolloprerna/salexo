<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadUdfData extends Model
{
    use HasFactory;
    protected $fillable = [
        'lead_id',
        'udf_id',
        'value',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'strIP'
    ];
}
