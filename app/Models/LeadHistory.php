<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadHistory extends Model
{
    use HasFactory;
    public $table = 'lead_history';
    protected $fillable = [
        'iLeadHistoryId',
        'iLeadId',
        'iCustomerId',
        'Comments',
        'followup_by',
        'next_followup_date',
        'status',
        'cancel_reason_id',
        'amount',
        'iEnterBy',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at'
    ];
}
