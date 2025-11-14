<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;
    public $table = 'invoicedetails';
    protected $fillable = [
        'invoicedetailsId', 'invoiceID', 'productID', 'description', 'uom', 'quantity', 'rate', 'amount', 'discount', 'netAmount', 'totalAmount', 'size', 'iGstPercentage',
    ];

     public function service()
    {
        return $this->belongsTo(Service::class, 'productID', 'service_id');
    }
}