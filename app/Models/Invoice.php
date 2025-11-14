<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    public $table = 'invoice';
   protected $primaryKey = 'invoiceId';
    protected $fillable = [
        'iYearId',
        'iInvoiceNo',
        'iPartyId',
        'iCompanyId',
        'iType',
        'iInvoiceAmount',
        'strIP',
        'strEntryDate',
        'iGstType',
        'strTermsCondition'
    ];

    protected $casts = [
        'entryDate' => 'datetime:d-m-Y',
    ];
    public static function getNextInvoiceNo($companyId)
    {
        $latestInvoice = self::where('iCompanyId', $companyId)->orderBy('invoiceId', 'DESC')->first();
        if ($latestInvoice) {
            return $latestInvoice->iInvoiceNo + 1;
        } else {
            return 1; // Start from 1 if no Invoice exists for this company.
        }
    }
    public function company()
    {
        return $this->belongsTo(CompanyClient::class, 'iCompanyId', 'company_id');
    }
    public function party()
    {
        return $this->belongsTo(CompanyClient::class, 'partyId', 'iPartyId');
    }
}
