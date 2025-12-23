<?php  

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\LeadMaster;

class CalenderLeadApiController extends Controller
{
    public function getLeads(Request $request)
    {
        try {
            $employee = Auth::guard('employee_api')->user();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 401);
            }

            $employeeId = $request->employee_id;
            $companyId = $employee->company_id;

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
                    // Incoming format:  "10-07-2025 12:00 PM"
                    $carbonDate = \Carbon\Carbon::createFromFormat('d-m-Y h:i A', $lead->next_followup_date);

                    return [
                        "date" => $carbonDate->format('m-d-Y'), // Required format
                        "lead" => 'Lead: ' . $lead->customer_name . ' with ' . $lead->emp_name,
                    ];
                } catch (\Exception $e) {
                    Log::warning('Invalid date format: ' . $lead->next_followup_date);
                    return null;
                }
            })->filter()->values();

            return response()->json([
            'success'        => true,
            'message'        => 'Calender Lead',
            'calender_lead' =>$data,
            ]);

        } catch (\Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch lead list',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
