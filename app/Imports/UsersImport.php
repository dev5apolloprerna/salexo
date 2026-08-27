<?php

namespace App\Imports;

use App\Models\LeadMaster;
use App\Models\LeadSource;
use App\Models\Employee;
use App\Models\LeadPipeline;
use App\Models\Service;
use App\Models\LeadUdfData;
use App\Models\UdfMaster;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class UsersImport implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows
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

    protected $companyId;
    protected $udfs;

    public function __construct($companyId)
    {
        $this->companyId = $companyId;

        $this->udfs = UdfMaster::where('company_id', $companyId)
            ->where('isDelete', 0)
            ->where('iStatus', 1)
            ->orderBy('id')
            ->get();

        $this->expectedHeadings = array_merge(
            $this->expectedHeadings,
            $this->udfs->map(function ($udf) {
                return Str::slug($udf->label, '_');
            })->all()
        );

        // Prevent ambiguous imports when two UDF labels resolve to the same
        // heading key, or when a UDF collides with a built-in column name.
        $this->assertExpectedHeadingsAreUnique();
    }

    public function rules(): array
    {
        $rules = [
            'contact_person_name' => 'required|string|max:255',
            'mobile' => 'required|digits:10',
            'remarks' => 'required|string|max:255',
            'service_product' => 'required|string|max:255',
            'lead_source' => 'required|string|max:255',
            'employee' => 'required|string|max:255',
        ];

        foreach ($this->udfs as $udf) {
            $heading = Str::slug($udf->label, '_');

            $rules[$heading] = $udf->required === 'Yes'
                ? 'required|string|max:255'
                : 'nullable|string|max:255';
        }

        return $rules;
    }

    /**
     * Validate the heading keys in a way that works for every client.
     *
     * - Base headings are always required.
     * - Active UDF headings are company-specific and are added automatically.
     * - Column order does not matter because rows are read by heading key.
     * - Empty numeric/unnamed fallback columns created by Excel/Laravel Excel
     *   are ignored only when the whole column is empty.
     * - A numeric/unnamed column that contains data is rejected.
     */
    protected function validateHeadings(Collection $rows): void
    {
        $rawHeadings = array_keys($rows->first()->toArray());
        $actualHeadings = [];

        foreach ($rawHeadings as $rawHeading) {
            $normalizedHeading = $this->normalizeHeading($rawHeading);

            $isExpectedNumericHeading = $normalizedHeading !== ''
                && in_array($normalizedHeading, $this->expectedHeadings, true);

            $isFallbackHeading = $normalizedHeading === ''
                || ((is_int($rawHeading) || ctype_digit((string) $rawHeading))
                    && !$isExpectedNumericHeading);

            // Laravel Excel / PhpSpreadsheet can expose a trailing blank column
            // as a numeric key (for example 18 for the 19th column). Ignore it
            // only when that entire column is actually empty.
            if ($isFallbackHeading && $this->columnIsCompletelyEmpty($rows, $rawHeading)) {
                continue;
            }

            $actualHeadings[] = $normalizedHeading;
        }

        $missing = array_values(array_diff($this->expectedHeadings, $actualHeadings));
        $extra = array_values(array_diff($actualHeadings, $this->expectedHeadings));

        $actualDuplicates = $this->duplicateValues($actualHeadings);

        if (empty($missing) && empty($extra) && empty($actualDuplicates)) {
            return;
        }

        $messages = [];

        if (!empty($missing)) {
            $messages[] = 'Missing headers: ' . implode(', ', $missing) . '.';
        }

        if (!empty($extra)) {
            $messages[] = 'Unexpected headers: '
                . implode(', ', array_map([$this, 'displayHeading'], $extra))
                . '.';
        }

        if (!empty($actualDuplicates)) {
            $messages[] = 'Duplicate headers after formatting: '
                . implode(', ', array_map([$this, 'displayHeading'], $actualDuplicates))
                . '.';
        }

        $messages[] = 'Expected headers: ' . implode(', ', $this->expectedHeadings) . '.';

        throw ValidationException::withMessages([
            'file' => $messages,
        ]);
    }

    /**
     * Keep header formatting consistent with Laravel Excel's default
     * WithHeadingRow "slug" formatter.
     */
    protected function normalizeHeading($heading): string
    {
        return Str::slug(trim((string) $heading), '_');
    }

    /**
     * A fallback/unnamed column is harmless only if every imported row has
     * no value in that column.
     */
    protected function columnIsCompletelyEmpty(Collection $rows, $heading): bool
    {
        foreach ($rows as $row) {
            $value = $row[$heading] ?? null;

            if (is_array($value)) {
                foreach ($value as $nestedValue) {
                    if ($nestedValue !== null && trim((string) $nestedValue) !== '') {
                        return false;
                    }
                }

                continue;
            }

            if ($value !== null && trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * Reject UDF configuration collisions such as:
     * "Property Type" + "Property-Type" => property_type
     * or a UDF named "Email" => email (built-in heading collision).
     */
    protected function assertExpectedHeadingsAreUnique(): void
    {
        $duplicates = $this->duplicateValues($this->expectedHeadings);

        if (empty($duplicates)) {
            return;
        }

        throw ValidationException::withMessages([
            'file' => [
                'Import configuration error. Duplicate header key(s): '
                . implode(', ', $duplicates)
                . '. Rename the conflicting UDF field(s).'
            ],
        ]);
    }

    protected function duplicateValues(array $values): array
    {
        $counts = array_count_values($values);

        return array_values(array_keys(array_filter($counts, function ($count) {
            return $count > 1;
        })));
    }

    protected function displayHeading($heading): string
    {
        return $heading === '' ? '(unnamed column)' : (string) $heading;
    }

    public function collection(Collection $rows)
    {
        /*
        |--------------------------------------------------------------------------
        | Check empty file
        |--------------------------------------------------------------------------
        */

        if ($rows->isEmpty()) {
            throw ValidationException::withMessages([
                'file' => 'The uploaded file is empty.'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Excel / CSV headings
        |--------------------------------------------------------------------------
        */

        $this->validateHeadings($rows);

        /*
        |--------------------------------------------------------------------------
        | First Pass
        | Validate employee + database duplicate + CSV duplicate
        |--------------------------------------------------------------------------
        */

        $errors = [];
        $uploadedContacts = [];

        foreach ($rows as $index => $row) {

            $rowNumber = $index + 2;

            /*
            |--------------------------------------------------------------------------
            | Check employee
            |--------------------------------------------------------------------------
            */

            $employee = Employee::where(
                'company_id',
                $this->companyId
            )
                ->where(
                    'emp_name',
                    trim((string) $row['employee'])
                )
                ->first();

            if (!$employee) {
                $errors[] =
                    "Row {$rowNumber}: Employee '{$row['employee']}' does not exist.";
            }

            /*
            |--------------------------------------------------------------------------
            | Check duplicate from database
            |--------------------------------------------------------------------------
            */

            $duplicate = LeadMaster::findActiveDuplicate(
                $this->companyId,
                $row['contact_person_name'] ?? '',
                $row['mobile'] ?? '',
                $row['email'] ?? ''
            );

            if ($duplicate) {
                $errors[] =
                    "Row {$rowNumber}: "
                    . LeadMaster::duplicateErrorMessage($duplicate);
            }

            /*
            |--------------------------------------------------------------------------
            | Normalize current CSV contact
            |--------------------------------------------------------------------------
            */

            $contact = [
                'row' => $rowNumber,

                'name' => mb_strtolower(
                    trim(
                        (string) ($row['contact_person_name'] ?? '')
                    )
                ),

                'mobile' => preg_replace(
                    '/\D+/',
                    '',
                    (string) ($row['mobile'] ?? '')
                ),

                'email' => mb_strtolower(
                    trim(
                        (string) ($row['email'] ?? '')
                    )
                ),
            ];

            /*
            |--------------------------------------------------------------------------
            | Check duplicate inside same CSV
            |--------------------------------------------------------------------------
            */

            $duplicateRow = collect($uploadedContacts)
                ->first(function ($uploaded) use ($contact) {

                    return $uploaded['name'] === $contact['name']
                        && (
                            (
                                $contact['mobile'] !== ''
                                && $uploaded['mobile'] === $contact['mobile']
                            )
                            ||
                            (
                                $contact['email'] !== ''
                                && $uploaded['email'] === $contact['email']
                            )
                        );
                });

            if ($duplicateRow) {

                $errors[] =
                    "Row {$rowNumber}: Duplicate of CSV row {$duplicateRow['row']}"
                    . " (Contact: "
                    . ($row['contact_person_name'] ?? '')
                    . " | Mobile: "
                    . ($row['mobile'] ?? '')
                    . ").";

            } else {

                $uploadedContacts[] = $contact;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Stop import if any validation error exists
        |--------------------------------------------------------------------------
        */

        if (!empty($errors)) {
            throw ValidationException::withMessages([
                'csv_rows' => $errors
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Start transaction only after all rows are validated
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Get New Lead Pipeline
            |--------------------------------------------------------------------------
            */

            $newLead = LeadPipeline::where([
                'company_id' => $this->companyId,
                'pipeline_name' => 'New Lead'
            ])->first();

            /*
            |--------------------------------------------------------------------------
            | Insert rows
            |--------------------------------------------------------------------------
            */

            foreach ($rows as $row) {

                $companyId = $this->companyId;

                /*
                |--------------------------------------------------------------------------
                | Get Employee
                |--------------------------------------------------------------------------
                */

                $employee = Employee::where(
                    'company_id',
                    $companyId
                )
                    ->where(
                        'emp_name',
                        trim((string) $row['employee'])
                    )
                    ->first();

                if (!$employee) {
                    throw ValidationException::withMessages([
                        'employee' =>
                            "Employee '{$row['employee']}' does not exist in the system."
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Create / Get Service
                |--------------------------------------------------------------------------
                */

                $serviceName = trim(
                    ucwords(
                        strtolower(
                            (string) $row['service_product']
                        )
                    )
                );

                $service = Service::firstOrCreate(
                    [
                        'company_id' => $companyId,
                        'service_name' => $serviceName,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                $serviceId = $service->service_id;

                /*
                |--------------------------------------------------------------------------
                | Create / Get Lead Source
                |--------------------------------------------------------------------------
                */

                $sourceName = trim(
                    ucwords(
                        strtolower(
                            (string) $row['lead_source']
                        )
                    )
                );

                $source = LeadSource::firstOrCreate(
                    [
                        'company_id' => $companyId,
                        'lead_source_name' => $sourceName,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                $sourceId = $source->lead_source_id;

                /*
                |--------------------------------------------------------------------------
                | Create Lead
                |--------------------------------------------------------------------------
                */

                $lead = LeadMaster::create([

                    'iCustomerId' => $companyId,

                    'iemployeeId' => Auth::user()->emp_id ?? 0,

                    'company_name' =>
                        $row['company_name'] ?? '',

                    'GST_No' =>
                        $row['gst'] ?? '',

                    'customer_name' =>
                        $row['contact_person_name'] ?? '',

                    'email' =>
                        $row['email'] ?? '',

                    'mobile' =>
                        $row['mobile'] ?? '',

                    'address' =>
                        $row['address'] ?? '',

                    'alternative_no' =>
                        $row['alternate_number'] ?? '',

                    'remarks' =>
                        $row['remarks'] ?? '',

                    'product_service_id' =>
                        $serviceId,

                    'LeadSourceId' =>
                        $sourceId,

                    'lead_history_id' =>
                        0,

                    'followup_by' =>
                        0,

                    'status' =>
                        $newLead->pipeline_id ?? 0,

                    'cancel_reason_id' =>
                        0,

                    'employee_id' =>
                        $employee->emp_id,

                    'initially_contacted' =>
                        0,

                    'iEnterBy' =>
                        Auth::user()->emp_id ?? 0,

                    'created_at' =>
                        now(),

                    'updated_at' =>
                        now(),
                ]);

                /*
                |--------------------------------------------------------------------------
                | Save UDF Values
                |--------------------------------------------------------------------------
                */

                foreach ($this->udfs as $udf) {

                    $heading = Str::slug(
                        $udf->label,
                        '_'
                    );

                    $value = $row[$heading] ?? null;

                    if (
                        $value !== null
                        && trim((string) $value) !== ''
                    ) {

                        LeadUdfData::create([
                            'lead_id' => $lead->lead_id,
                            'udf_id' => $udf->id,
                            'value' => $value,
                            'created_at' => now(),
                        ]);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Everything successful
            |--------------------------------------------------------------------------
            */

            DB::commit();

        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }
}