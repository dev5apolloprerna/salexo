<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\LeadMaster;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmployerFollowupMessageCron extends Command
{
    protected $signature = 'employer_followup:cron';
    protected $description = 'Command description';

    public function handle()
    {
        Log::info('Employer Followup Cron Executed');

        // Get all employees
        $employees = Employee::select('employee_master.*', 'company_client_master.company_name')
            ->where('isCompanyAdmin', 0)
            ->join('company_client_master', 'company_client_master.company_id', '=', 'employee_master.company_id')
            ->get();

        foreach ($employees as $employee) {
            // Get all leads assigned to this employee
            $allLeads = LeadMaster::where('iemployeeId', $employee->emp_id)
                ->where(['iStatus' => 1, 'isDelete' => 0])
                ->get();

            // Count today's follow-ups
            $todaysFollowupCount = $allLeads->filter(function ($lead) {
                try {
                    if (!$lead->next_followup_date) return false;
                    $date = Carbon::createFromFormat('d-m-Y h:i A', trim($lead->next_followup_date));
                    return $date->isToday();
                } catch (\Exception $e) {
                    return false;
                }
            })->count();

            // Count overdue follow-ups
            $overdueFollowupCount = $allLeads->filter(function ($lead) {
                try {
                    if (!$lead->next_followup_date) return false;
                    $date = Carbon::createFromFormat('d-m-Y h:i A', trim($lead->next_followup_date));
                    return $date->lt(today());
                } catch (\Exception $e) {
                    return false;
                }
            })->count();

            $whatsappToken = config('app.whatsapp_token');
            $phoneNumberId = config('app.whatsapp_phone_id');
            $recipient = '+91' . $employee->emp_mobile;

            try {
                $response = Http::withToken($whatsappToken)->post("https://graph.facebook.com/v19.0/{$phoneNumberId}/messages", [
                    "messaging_product" => "whatsapp",
                    "to" => $recipient,
                    "type" => "template",
                    "template" => [
                        "name" => "employer_followup_message",
                        "language" => [
                            "code" => "en"
                        ],
                        "components" => [
                            [
                                "type" => "body",
                                "parameters" => [
                                    [
                                        "type" => "text",
                                        "text" => $employee->emp_name
                                    ],
                                    [
                                        "type" => "text",
                                        "text" => $employee->company_name
                                    ],
                                    [
                                        "type" => "text",
                                        "text" => (string) $todaysFollowupCount
                                    ],
                                    [
                                        "type" => "text",
                                        "text" => (string) $overdueFollowupCount
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);

                if (!$response->successful()) {
                    Log::error('WhatsApp API Error', [
                        'employee_id' => $employee->emp_id,
                        'status' => $response->status(),
                        'body' => $response->json()
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('WhatsApp Sending Failed: ' . $e->getMessage());
            }
        }

        Log::info('Employer Followup Cron Completed');

        return Command::SUCCESS;
    }
}
