<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestForDemo extends Model
{
    protected $table = 'request_for_demo';
    protected $primaryKey = 'id'; // Define the primary key

    protected $fillable = [
        'company_name',
        'contact_person_name',
        'mobile',
        'email',
        'situable_time',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'strIP'
    ];
}
