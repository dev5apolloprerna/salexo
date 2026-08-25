<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

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

    protected static function booted()
    {
        static::creating(function (LeadMaster $lead) {
            $contactName = trim((string) $lead->customer_name);
            $mobile = preg_replace('/\D+/', '', (string) $lead->mobile);
            $email = mb_strtolower(trim((string) $lead->email));

            if ($contactName === '' || ($mobile === '' && $email === '')) {
                return;
            }

            $activeLeads = static::query()
                ->where('iCustomerId', $lead->iCustomerId)
                ->where('isDelete', 0);

            $hasContactLead = $activeLeads
                ->whereRaw('LOWER(TRIM(customer_name)) = ?', [mb_strtolower($contactName)])
                ->where(function ($query) use ($mobile, $email) {
                    if ($mobile !== '') {
                        $query->whereRaw(
                            "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(mobile, ' ', ''), '-', ''), '(', ''), ')', ''), '+', ''), '.', '') = ?",
                            [$mobile]
                        );
                    }

                    if ($email !== '') {
                        $method = $mobile !== '' ? 'orWhereRaw' : 'whereRaw';
                        $query->{$method}('LOWER(TRIM(email)) = ?', [$email]);
                    }
                })
                ->exists();

            if ($hasContactLead) {
                throw ValidationException::withMessages([
                    'customer_name' => 'This contact person already has an active lead with the same mobile number or email. Mark it as Deal Done or Deal Cancelled before creating another lead.',
                ]);
            }
        });
    }

    public function State()
    {
        return $this->belongsTo(State::class, 'state_id', 'stateId');
    }

    public function LeadSource()
    {
        return $this->belongsTo(LeadSource::class, 'LeadSourceId', 'lead_source_id');
    }
}
