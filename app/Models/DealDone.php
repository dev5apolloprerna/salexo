<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class DealDone extends Model
{

    use HasFactory;
    public $table = 'deal_done';
    protected $fillable = [
        'lead_id',
        'iCustomerId',
        'company_name',
        'GST_No',
        'customer_name',
        'email',
        'mobile',
        'alternative_no',
        'remarks',
        'address',
        'product_service_id',
        'LeadSourceId',
        'lead_history_id',
        'iemployeeId',
        'comments',
        'followup_by',
        'next_followup_date',
        'status',
        'CurrentLeadStatusId',
        'cancel_reason_id',
        'employee_id',
        'initially_contacted',
        'amount',
        'iEnterBy',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'deal_done_at',
        'deal_cancel_at',

    ];

    public function State()
    {
        return $this->belongsTo(State::class, 'state_id', 'stateId');
    }

    public function LeadSource()
    {
        return $this->belongsTo(LeadSource::class, 'LeadSourceId', 'lead_source_id');
    }
}
