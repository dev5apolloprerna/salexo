<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    public $table = 'notifications';
    protected $fillable = [
        'id',
        'getId',
        'title',
        'name',
        'body',
        'guid',
        'type',
        'service',
        'iTripId',
        'fcm_token',
        'status',
        'response',
        'created_at',
        'updated_at',
        'strIP'
    ];
}
