<?php
namespace App\Repositories\Report;

use App\Models\CompanyClient;
use App\Repositories\Report\CompanyReportRepositoryInterface;

class CompanyReportRepository implements CompanyReportRepositoryInterface
{
    public function getSubscriptionReport($startDate = null, $endDate = null)
    {
        $query = CompanyClient::with(['plan','state'])
            ->select([
                'company_name',
                'contact_person_name',
                'mobile',
                'email',
                'state_id',
                'city',
                'plan_id',
                'subscription_end_date',
            ]);

        // Apply conditions based on availability of dates
        if ($startDate && $endDate) {
            $query->whereBetween('subscription_end_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('subscription_end_date', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('subscription_end_date', '<=', $endDate);
        }

    return $query->paginate(10);
    }


}
