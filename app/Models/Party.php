<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    use HasFactory;
    public $table = 'party';
    protected $fillable = [
        'strPartyName',
        'iCompanyId',
        'address1',
        'address2',
        'address3',
        'strGST',
        'iMobile',
        'strEmail',
        'strIP',
        'strEntryDate',
    ];
}