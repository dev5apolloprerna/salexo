<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformaInvoiceDetail extends Model
{
    use HasFactory;
    public $table = 'performa_invoicedetails';
    protected $fillable = [
        'performainvoicedetailsId', 'performainvoiceID', 'productID', 'description', 'uom', 'quantity', 'rate', 'amount', 'discount', 'netAmount', 'totalAmount', 'size', 'iGstPercentage',
    ];

     public function service()
    {
        return $this->belongsTo(Service::class, 'productID', 'service_id');
    }
}