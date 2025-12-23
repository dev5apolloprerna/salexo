<?php

namespace App\Imports;

use App\Models\LeadMaster;
use App\Models\LeadSource;
use App\Models\Employee;
use App\Models\LeadPipeline;
use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\ValidationException;

class UsersImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $expectedHeadings = [
        "company_name",
        "gst",
        "contact_person_name",
        "email",
        "mobile",
        "alternate_number",
        "address",
        "remarks",
        "service_product",
        "lead_source",
        "employee"
    ];

    public function rules(): array
    {
        return [
            'contact_person_name' => 'required|string|max:255',
            'mobile' => 'required|digits:10',
            'remarks' => 'required|string|max:255',
            'service_product' => 'required|string|max:255',
            'lead_source' => 'required|string|max:255',
            'employee' => 'required|string|max:255',
        ];
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw ValidationException::withMessages(['file' => 'The uploaded file is empty.']);
        }

        // 🔍 Get actual headings from the first row keys
        $actualHeadings = array_keys($rows->first()->toArray());

        // Compare expected vs actual
        if ($this->expectedHeadings !== $actualHeadings) {
            throw ValidationException::withMessages([
                'file' => [
                    'Invalid CSV headers. Expected: ' . implode(', ', $this->expectedHeadings) .
                        ' | Found: ' . implode(', ', $actualHeadings)
                ]
            ]);
        }

        $errors = [];
        DB::beginTransaction();

        try {
            // Validate employee exists
            foreach ($rows as $index => $row) {
                $employee = Employee::where('emp_name', trim($row['employee']))->first();
                if (!$employee) {
                    $errors[] = "Row " . ($index + 2) . ": Employee '{$row['employee']}' does not exist.";
                }
            }

            if (!empty($errors)) {
                DB::rollBack();
                throw ValidationException::withMessages(['employee' => $errors]);
            }

            // ✅ Second pass: insert all rows
            foreach ($rows as $row) {

                // ✅ Employee must exist first
                $employee = Employee::where('emp_name', trim($row['employee']))->first();
                if (!$employee) {
                    throw ValidationException::withMessages([
                        'employee' => "Employee '{$row['employee']}' does not exist in the system."
                    ]);
                }

                $companyId = Auth::user()->company_id;

                $serviceName = trim(ucwords(strtolower($row['service_product'])));
                $service = Service::firstOrCreate(
                    ['company_id' => $companyId, 'service_name' => $serviceName,],
                    ['created_at' => now(), 'updated_at' => now(),]
                );
                $serviceId = $service->service_id;


                $sourceName = trim(ucwords(strtolower($row['lead_source'])));
                $source = LeadSource::firstOrCreate(
                    ['company_id' => $companyId, 'lead_source_name' => $sourceName,],
                    ['created_at' => now(), 'updated_at' => now(),]
                );
                $sourceId = $source->lead_source_id;

                $employee = Employee::where('emp_name', trim($row['employee']))->first();
                $new_lead = LeadPipeline::where([
                    'company_id' => Auth::user()->company_id,
                    'pipeline_name' => "New Lead"
                ])->first();

                LeadMaster::create([
                    'iCustomerId' => $companyId ?? 0,
                    'iemployeeId' => Auth::user()->emp_id ?? '0',
                    'company_name' => $row['company_name'] ?? '',
                    'GST_No' => $row['gst'] ?? '',
                    'customer_name' => $row['contact_person_name'] ?? '',
                    'email' => $row['email'] ?? '',
                    'mobile' => $row['mobile'] ?? '',
                    'address' => $row['address'] ?? '',
                    'alternative_no' => $row['alternate_number'] ?? '',
                    'remarks' => $row['remarks'] ?? '',
                    'product_service_id' => $serviceId,
                    'LeadSourceId' => $sourceId,
                    'lead_history_id' => 0,
                    'followup_by' => 0,
                    'status' => $new_lead->pipeline_id ?? 0,
                    'cancel_reason_id' => 0,
                    'employee_id' => $employee->emp_id ?? 0,
                    'initially_contacted' => 0,
                    'iEnterBy' => Auth::user()->emp_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            // ❌ Rollback if anything fails
            DB::rollBack();
            throw $e;
        }
    }
}
