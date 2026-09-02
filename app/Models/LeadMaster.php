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
            $duplicate = static::findActiveDuplicate(
                $lead->iCustomerId,
                $lead->customer_name,
                $lead->mobile,
                $lead->email
            );

            if ($duplicate) {
                throw ValidationException::withMessages([
                    'customer_name' => static::duplicateErrorMessage($duplicate),
                ]);
            }
        });
    }
    public function scopeActive($query)
    {
        return $query->where('isDelete', 0);
    }

    public static function findActiveDuplicate($companyId, $contactName, $mobile = null, $email = null): ?self
    {
        $contactName = trim((string) $contactName);
        $mobile = preg_replace('/\D+/', '', (string) $mobile);
        $email = mb_strtolower(trim((string) $email));

        if ($contactName === '' || ($mobile === '' && $email === '')) {
            return null;
        }

        return static::query()
            ->where('iCustomerId', $companyId)
            ->where('isDelete', 0)
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
            ->first();
    }

    public static function duplicateErrorMessage(self $lead): string
    {
        $details = [
            "Lead ID: {$lead->lead_id}",
            'Contact: ' . ($lead->customer_name ?: 'N/A'),
            'Mobile: ' . ($lead->mobile ?: 'N/A'),
        ];

        return 'An active lead already exists (' . implode(' | ', $details) . '). Mark it as Deal Done or Deal Cancelled before creating another lead.';
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
