<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'order';
    protected $primaryKey = 'id'; // Define the primary key

    protected $fillable = [
        'emp_id',
        'company_name',
        'contact_person_name',
        'gst',
        'email',
        'mobile',
        'address',
        'pincode',
        'city',
        'state_id',
        'plan_name',
        'duration_in_days',
        'amount',
        'gst_percentage',
        'gst_amount',
        'net_amount',
        'isPayment',
        'iStatus',
        'isDeleted',
        'created_at',
        'updated_at',
        'strIP'
    ];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'stateId');
    }
}
