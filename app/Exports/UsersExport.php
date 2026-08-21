<?php

namespace App\Exports;

use App\Models\UdfMaster;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements WithHeadings
{
     private $companyId;

    public function __construct($companyId)
    {
        $this->companyId = $companyId;
    }

    public function headings(): array
    {
        $headings = [
            "Company Name",
            "GST",
            "Contact Person Name",
            "Email",
            "Mobile",
            "Alternate Number",
            "Address",
            "Remarks",
            "Service / Product",
            "Lead Source",
            "Employee"
        ];
        $udfHeadings = UdfMaster::where('company_id', $this->companyId)
            ->where('isDelete', 0)
            ->where('iStatus', 1)
            ->orderBy('id')
            ->pluck('label')
            ->all();

        return array_merge($headings, $udfHeadings);
    }
}
