<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Employee;
use App\Models\CompanyClient;
use App\Models\LeadMaster;
use App\Models\LeadPipeline;
use Illuminate\Support\Facades\Log;

class CalenderLeadController extends Controller
{

    public function index()
    {

        try {
            $emp_id = Auth::guard('web_employees')->user()->company_id;

            $employees = Employee::where(['iStatus' => 1, 'isDelete' => 0])->get();
            // dd($employees);

            return view('company_client.calender.index', compact('employees'));
        } catch (\Exception $e) {
            Log::error('Error in HomeController@index: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function getLeads(Request $request)
    {
        try {
            $employeeId = $request->employee_id;
            $companyId = Auth::user()->company_id;

            $query = LeadMaster::query()
                ->where(['lead_master.iStatus' => 1, 'lead_master.isDelete' => 0])
                ->where('lead_master.iCustomerId', $companyId)
                ->join('employee_master', 'employee_master.emp_id', '=', 'lead_master.iemployeeId');

            if (!empty($employeeId)) {
                $query->where('lead_master.iemployeeId', $employeeId);
            }
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
