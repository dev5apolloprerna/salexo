<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\LeadMaster;
use Illuminate\Support\Facades\Log;

class EmployeeCalenderLeadController extends Controller
{

    public function index()
    {
        try {
            return view('employee.calender.index');
        } catch (\Exception $e) {
            Log::error('Error in HomeController@index: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function getLeads(Request $request)
    {
        try {

            $emp = Auth::guard('web_employees')->user();

            $query = LeadMaster::query()
                ->where([
                    'lead_master.iStatus' => 1,
                    'lead_master.isDelete' => 0,
                    'lead_master.iCustomerId' => $emp->company_id,
                    'lead_master.iEnterBy' => $emp->emp_id,
                    // 'lead_master.iemployeeId' => $emp->emp_id
                ])
                ->join('employee_master', 'employee_master.emp_id', '=', 'lead_master.iemployeeId');


            $leads = $query->get([
                'lead_master.next_followup_date',
                'lead_master.customer_name',
                'employee_master.emp_name'
            ]);

            $data = $leads->map(function ($lead) {
                try {
                    // Convert "10-07-2025 12:00 PM" to "2025-07-10T12:00:00"
                    $carbonDate = \Carbon\Carbon::createFromFormat('d-m-Y h:i A', $lead->next_followup_date);

                    return [
                        'title' => 'Lead: ' . $lead->customer_name . ' with ' . $lead->emp_name,
                        'start' => $carbonDate->toIso8601String(), // For FullCalendar
                    ];
                } catch (\Exception $e) {
                    Log::warning('Invalid date format for lead: ' . $lead->next_followup_date);
                    return null; // Skip invalid date
                }
            })->filter(); // Remove nulls

            return response()->json($data->values());
        } catch (\Exception $e) {
            Log::error('Error in CalenderLeadController@getLeads: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to load leads'], 500);
        }
    }
}
