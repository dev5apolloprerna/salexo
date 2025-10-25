<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\LeadMaster;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\PushNotificationController;

class DailyLeadCron extends Command
{
    protected $signature = 'run_daily_lead:cron';
    protected $description = 'Send follow-up notifications every 15 minutes.';

    public function handle()
    {
        $startTime = now()->addMinute(); // now + 1 minute
        $endTime = now()->addMinutes(16); // now + 16 minutes
        
        $notifier = new PushNotificationController();

        $employees = Employee::select('employee_master.*', 'company_client_master.company_name')
            ->where('isCompanyAdmin', 1)
            ->join('company_client_master', 'company_client_master.company_id', '=', 'employee_master.company_id')
            ->get();

        foreach ($employees as $employee) {
            
            // $allLeads = LeadMaster::where('iemployeeId', $employee->emp_id)
            //     ->where(['iStatus' => 1, 'isDelete' => 0])
            //     ->whereNotNull('next_followup_date')
            //     ->get();

            $allLeads = LeadMaster::select(
                    'lead_master.*',
                    'service_master.service_name'
                )
                ->where('lead_master.iemployeeId', $employee->emp_id)
                ->where(['lead_master.iStatus' => 1, 'lead_master.isDelete' => 0])
                ->whereNotNull('lead_master.next_followup_date')
                ->leftjoin('product_service_id', 'product_service_id.service_id', '=', 'lead_master.product_service_id')
                ->get();
        
            foreach ($allLeads as $lead) {
                try {
                    
                    $followupTime = Carbon::createFromFormat('d-m-Y h:i A', $lead->next_followup_date);

                    // Log::debug("Checking lead ID {$lead->lead_id} with follow-up time: {$followupTime}");

                    if ($followupTime->isToday()) {
                        Log::info("Lead matched for today: ID {$lead->lead_id}, time: {$followupTime}");
                    }

                    if (
                        $followupTime->isToday() &&
                        $followupTime->between($startTime, $endTime)
                    ) {


                        $appName = config('app.name');
                        $TextMessage = "Reminder: Follow up Reminder with " . $lead->customer_name . " for " . $lead->service_name . " at " . $lead->next_followup_date;
                        // $TextMessage = "Reminder: Follow up with " . $lead->customer_name . " for lead ID: " . $lead->lead_id . " at " . $lead->next_followup_date;

                        $array = [
                            "id" => $employee->emp_id,
                            'title' => $appName,
                            'body' => $TextMessage,
                            'guid' => '0',
                            'type' => "daily_lead",
                        ];

                        $notifier->notification($array);

                        Log::info('Notification sent', [
                            'employee_id' => $employee->emp_id,
                            'lead_id' => $lead->lead_id,
                            'next_followup_date' => $lead->next_followup_date
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::warning("Invalid date format for lead ID {$lead->id}: " . $lead->next_followup_date);
                }
            }
        }

        $this->info("Checked leads for follow-up between $startTime and $endTime");
    }
}
