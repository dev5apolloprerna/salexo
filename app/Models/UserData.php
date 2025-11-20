<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    use HasFactory;
    public $table = 'user_data';
    protected $primaryKey = 'user_id'; // Define the primary key

    protected $fillable = [
        'company_id',
        'emp_id',
        'source_id',
        'ad_id',
        'access_token',
        'verify_token',
        'api_id'
    ];

}
