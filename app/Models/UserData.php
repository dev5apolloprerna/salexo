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
        'ad_id',
        'api_id'
    ];

        public function company(){ return $this->belongsTo(CompanyClient::class, 'company_id','company_id'); }

}
