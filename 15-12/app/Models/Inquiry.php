<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;
    
    public $table = 'inquiry';
    protected $fillable = [
        'name',
        'mobileNumber',
        'email',
        'message',
        'strIp'
    ];
}
