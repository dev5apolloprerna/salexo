<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Payment extends Model
{
    public $table = 'card_payment';
    protected $dates = [
        'created_at',
        'updated_at',

    ];

    protected $fillable = [
        'id',
        'order_id',
        'oid',
        'razorpay_payment_id',
        'razorpay_order_id',
        'razorpay_signature',
        'receipt',
        'amount',
        'currency',
        'status',
        'iPaymentType',
        'Remarks',
        'created_at',
        'updated_at',

    ];
}
