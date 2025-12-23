<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    use HasFactory;
    public $table = 'user_data';
    protected $primaryKey = 'data_id'; // Define the primary key

    protected $fillable = [
        'company_id',
        'emp_id',
        'source_id',
        'product_id',
        'ad_id',
        'api_id',
        'assign_type'
    ];

        public function company(){ return $this->belongsTo(CompanyClient::class, 'company_id','company_id'); }
        public function employee(){ return $this->belongsTo(Employee::class, 'emp_id','emp_id'); }
        public function product(){ return $this->belongsTo(Service::class, 'product_id','service_id'); }
        public function source(){ return $this->belongsTo(LeadSource::class, 'source_id','lead_source_id'); }

}
