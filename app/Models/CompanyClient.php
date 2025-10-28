<?php

// app/Models/CompanyClient.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyClient extends Model
{
    protected $table = 'company_client_master';
    protected $primaryKey = 'company_id'; // Define the primary key


    protected $fillable = [
        'company_name',
        'GST',
        'contact_person_name',
        'mobile',
        'email',
        'Address',
        'pincode',
        'city',
        'state_id',
        'password',
        'subscription_start_date',
        'subscription_end_date',
        'plan_id',
        'plan_amount',
        'plan_days',
        'payment_terms',
        'delivery_terms',
        'terms_condition',
        'no_of_users',
        'iStatus',
        'isDeleted',
        'created_at',
        'updated_at'
    ];

    public function Plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'stateId');
    }
    public function employee()
    {
        return $this->hasOne(Employee::class, 'company_id', 'company_id');
    }
}
