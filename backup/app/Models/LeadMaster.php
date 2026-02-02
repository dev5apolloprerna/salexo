<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class LeadMaster extends Model
{

    use HasFactory;
    public $table = 'lead_master';
    protected $primaryKey = 'lead_id'; // Define the primary key
    public $timestamps = false; // Disable timestamps
    protected $fillable = [

        'iCustomerId',
        'iemployeeId',
        'company_name',
        'GST_No',
        'customer_name',
        'email',
        'mobile',
        'address',
        'alternative_no',
        'remarks',
        'product_service_id',
        'product_service_other',
        'LeadSourceId',
        'LeadSource_other',
        'lead_history_id',
        'comments',
        'followup_by',
        'next_followup_date',
        'status',
        'cancel_reason_id',
        'amount',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'employee_id',
        'initially_contacted',
        'iEnterBy',
        'deal_converted_at',
        'json',
        'link',

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
