<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformaInvoice extends Model
{
    use HasFactory;
    public $table = 'performa_invoice';
   protected $primaryKey = 'performainvoiceId ';
    protected $fillable = [
        'iYearId',
        'iPerformaInvoiceNo',
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
        $latestInvoice = self::where('iCompanyId', $companyId)->orderBy('performainvoiceId ', 'DESC')->first();
        if ($latestInvoice) {
            return $latestInvoice->iPerformaInvoiceNo + 1;
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
