<?php

namespace App\Exports;

use App\Models\User;
use App\Models\DummyExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements WithHeadings
{
    public function headings(): array
    {
        return [
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
    }
}
