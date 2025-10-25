<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ROIReportExport implements FromView
{
    protected $reportData;

    public function __construct($reportData)
    {
        $this->reportData = $reportData;
    }

    public function view(): View
    {
        return view('exports.roi_report', [
            'reportData' => $this->reportData
        ]);
    }
}
