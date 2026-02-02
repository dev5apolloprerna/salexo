<?php
namespace App\Repositories\Report;

interface CompanyReportRepositoryInterface
{
    public function getSubscriptionReport($startDate, $endDate);
}
