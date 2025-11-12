<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationDetail extends Model
{
    use HasFactory;
    public $table = 'quotationdetails';
    protected $fillable = [
        'quotationdetailsId', 'quotationID', 'productID', 'description', 'uom', 'quantity', 'rate', 'amount', 'discount', 'netAmount', 'totalAmount', 'size', 'iGstPercentage',
    ];

     public function service()
    {
        return $this->belongsTo(service::class, 'productID', 'service_id');
    }
}