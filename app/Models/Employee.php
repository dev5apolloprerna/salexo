<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Employee extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'employee_master';
    protected $primaryKey = 'emp_id'; // Define the primary key

    protected $fillable = [
        'company_id',
        'guid',
        'emp_name',
        'emp_mobile',
        'emp_email',
        'emp_loginId',
        'password',
        'isCompanyAdmin',
        'can_access_LMS',
        'role_id',
        'iStatus',
        'isDelete',
        'last_login',
        'otp',
        'otp_expire_time',
        'firebaseDeviceToken',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
