<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationDetail extends Model
{
    use HasFactory;
    public $table = 'quotationdetails';
    protected $fillable = [
        'description',
        'size',
        'rate',
        'quantity',
        'amount',
        'totalAmount',
        'strIP',
        'strEntryDate',
        'iGstPercentage'
    ];
}