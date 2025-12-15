<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Repositories\Report\CompanyReportRepositoryInterface;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $companyReport;

    public function __construct(CompanyReportRepositoryInterface $companyReport)
    {
        $this->companyReport = $companyReport;
    }

   public function subscriptionReport(Request $request)
    {


        try 
        {
            $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : null;
            $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : null;
        } catch (\Exception $e) {
            dd("Invalid date format", $e->getMessage());
        }

        // Flexible date range handling
        $reportData = $this->companyReport->getSubscriptionReport($startDate, $endDate);
        $startDate=$request->start_date;
        $endDate=$request->end_date;

        return view('admin.reports.subscription_report', compact('reportData', 'startDate', 'endDate'));
    }


}
