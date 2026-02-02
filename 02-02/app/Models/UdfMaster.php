<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UdfMaster extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'label',
        'required',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'strIP',
    ];
}
