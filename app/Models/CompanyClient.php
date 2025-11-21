<?php

// app/Models/CompanyClient.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyClient extends Model
{
    protected $table = 'company_client_master';
    protected $primaryKey = 'company_id'; // Define the primary key


    protected $fillable = [
         'company_id', 'company_name', 'GST', 'contact_person_name', 'mobile', 'email', 'Address', 'pincode', 'city', 'state_id', 'password', 'subscription_start_date', 'subscription_end_date', 'plan_id', 'plan_amount', 'plan_days', 'delivery_terms', 'payment_terms', 'terms_condition', 'isNotifyApi', 'company_logo', 'companyTemplate', 'no_of_users'
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


