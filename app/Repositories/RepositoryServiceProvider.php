<?php

namespace App\Repositories;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Repositories\Roles\RolesRepositoryInterface;
use App\Repositories\Users\UsersRepositoryInterface;
use App\Repositories\Plan\PlanRepositoryInterface;
use App\Repositories\State\StateRepositoryInterface;
use App\Repositories\Company\CompanyClientRepositoryInterface;
use App\Repositories\Employee\EmployeeRepositoryInterface;
use App\Repositories\Report\CompanyReportRepositoryInterface;
use App\Repositories\LeadSource\LeadSourceRepositoryInterface;
use App\Repositories\LeadCancelReason\LeadCancelReasonRepositoryInterface;
use App\Repositories\LeadPipeline\LeadPipelineRepositoryInterface;
use App\Repositories\Service\ServiceRepositoryInterface;
use App\Repositories\Lead\LeadRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
         $this->app->bind(
            'App\Repositories\Plan\PlanRepositoryInterface',
            'App\Repositories\Plan\PlanRepository'
        );
        
        $this->app->bind(
            'App\Repositories\State\StateRepositoryInterface',
            'App\Repositories\State\StateRepository'
        );
        
        $this->app->bind(
            'App\Repositories\Company\CompanyClientRepositoryInterface',
            'App\Repositories\Company\CompanyClientRepository'
        );
        
        $this->app->bind(
            'App\Repositories\Employee\EmployeeRepositoryInterface',
            'App\Repositories\Employee\EmployeeRepository'
        );

        $this->app->bind(
            'App\Repositories\Report\CompanyReportRepositoryInterface',
            'App\Repositories\Report\CompanyReportRepository'
        );
        $this->app->bind(
            'App\Repositories\LeadSource\LeadSourceRepositoryInterface',
            'App\Repositories\LeadSource\LeadSourceRepository'
        );
        $this->app->bind(
            'App\Repositories\LeadCancelReason\LeadCancelReasonRepositoryInterface',
            'App\Repositories\LeadCancelReason\LeadCancelReasonRepository'
        );
        $this->app->bind(
            'App\Repositories\LeadPipeline\LeadPipelineRepositoryInterface',
            'App\Repositories\LeadPipeline\LeadPipelineRepository'
        );
        $this->app->bind(
            'App\Repositories\Service\ServiceRepositoryInterface',
            'App\Repositories\Service\ServiceRepository'
        );
        $this->app->bind(
            'App\Repositories\Lead\LeadRepositoryInterface',
            'App\Repositories\Lead\LeadRepository'
        );
    
    }
}
