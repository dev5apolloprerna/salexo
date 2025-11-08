<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;
    public $table = 'quotation';
   protected $primaryKey = 'quotationId';
    protected $fillable = [
        'iYearId',
        'iQuotationNo',
        'iPartyId',
        'iCompanyId',
        'iType',
        'iQuotationAmount',
        'strIP',
        'strEntryDate',
        'iGstType',
        'strTermsCondition'
    ];

    protected $casts = [
        'entryDate' => 'datetime:d-m-Y',
    ];
    public static function getNextQuotationNo($companyId)
    {
        $latestQuotation = self::where('iCompanyId', $companyId)->orderBy('quotationId', 'DESC')->first();
        if ($latestQuotation) {
            return $latestQuotation->iQuotationNo + 1;
        } else {
            return 1; // Start from 1 if no quotation exists for this company.
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
