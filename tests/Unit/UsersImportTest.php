<?php

namespace Tests\Unit;

use App\Imports\UsersImport;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UsersImportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        app('db')->purge('sqlite');

        Schema::create('udf_masters', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->string('label');
            $table->string('required');
            $table->boolean('iStatus')->default(true);
            $table->boolean('isDelete')->default(false);
        });

        Schema::create('employee_master', function (Blueprint $table) {
            $table->increments('emp_id');
            $table->unsignedInteger('company_id');
            $table->string('emp_name');
        });

        Schema::create('lead_master', function (Blueprint $table) {
            $table->increments('lead_id');
            $table->unsignedInteger('iCustomerId');
            $table->string('customer_name')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->boolean('isDelete')->default(false);
        });

        DB::table('employee_master')->insert([
            'company_id' => 10,
            'emp_name' => 'Sales User',
        ]);
    }

    public function test_it_reports_all_duplicates_before_inserting_any_csv_rows(): void
    {
        DB::table('lead_master')->insert([
            'iCustomerId' => 10,
            'customer_name' => 'Existing Person',
            'mobile' => '9999999999',
            'email' => null,
        ]);

        $rows = new Collection([
            $this->row('New Person', '8888888888'),
            $this->row('New Person', '88888-88888'),
            $this->row('Existing Person', '9999999999'),
        ]);

        try {
            (new UsersImport(10))->collection($rows);
            $this->fail('The CSV should have been rejected.');
        } catch (ValidationException $exception) {
            $errors = $exception->errors()['csv_rows'];

            $this->assertCount(2, $errors);
            $this->assertStringContainsString('Row 3: Duplicate of CSV row 2', $errors[0]);
            $this->assertStringContainsString('Row 4: An active lead already exists (Lead ID: 1', $errors[1]);
            $this->assertDatabaseCount('lead_master', 1);
        }
    }

    public function test_it_ignores_an_empty_numeric_fallback_column(): void
    {
        DB::table('lead_master')->insert([
            'iCustomerId' => 10,
            'customer_name' => 'Existing Person',
            'mobile' => '9999999999',
            'email' => null,
        ]);

        $row = $this->row('Existing Person', '9999999999');
        $row->put(18, null);

        try {
            (new UsersImport(10))->collection(new Collection([$row]));
            $this->fail('The duplicate lead should have been rejected.');
        } catch (ValidationException $exception) {
            $errors = $exception->errors();

            $this->assertArrayHasKey('csv_rows', $errors);
            $this->assertArrayNotHasKey('file', $errors);
            $this->assertStringContainsString('An active lead already exists', $errors['csv_rows'][0]);
        }
    }

    public function test_it_rejects_a_numeric_fallback_column_when_it_contains_data(): void
    {
        $row = $this->row('New Person', '8888888888');
        $row->put(18, 'unexpected value');

        try {
            (new UsersImport(10))->collection(new Collection([$row]));
            $this->fail('The unexpected column should have been rejected.');
        } catch (ValidationException $exception) {
            $errors = $exception->errors();

            $this->assertArrayHasKey('file', $errors);
            $this->assertStringContainsString('Unexpected headers: 18', implode(' ', $errors['file']));
        }
    }

    public function test_it_requires_active_client_udf_headers(): void
    {
        DB::table('udf_masters')->insert([
            'company_id' => 10,
            'label' => 'Zone',
            'required' => 'No',
            'iStatus' => 1,
            'isDelete' => 0,
        ]);

        try {
            (new UsersImport(10))->collection(new Collection([
                $this->row('New Person', '8888888888'),
            ]));
            $this->fail('The missing client UDF header should have been rejected.');
        } catch (ValidationException $exception) {
            $errors = $exception->errors();

            $this->assertArrayHasKey('file', $errors);
            $this->assertStringContainsString('Missing headers: zone', implode(' ', $errors['file']));
        }
    }

    public function test_it_rejects_udf_names_that_collide_with_builtin_headers(): void
    {
        DB::table('udf_masters')->insert([
            'company_id' => 10,
            'label' => 'Email',
            'required' => 'No',
            'iStatus' => 1,
            'isDelete' => 0,
        ]);

        try {
            new UsersImport(10);
            $this->fail('The duplicate UDF heading should have been rejected.');
        } catch (ValidationException $exception) {
            $errors = $exception->errors();

            $this->assertArrayHasKey('file', $errors);
            $this->assertStringContainsString('Duplicate header key(s): email', implode(' ', $errors['file']));
        }
    }

    public function test_column_order_does_not_matter(): void
    {
        DB::table('lead_master')->insert([
            'iCustomerId' => 10,
            'customer_name' => 'Existing Person',
            'mobile' => '9999999999',
            'email' => null,
        ]);

        $reordered = new Collection(array_reverse(
            $this->row('Existing Person', '9999999999')->toArray(),
            true
        ));

        try {
            (new UsersImport(10))->collection(new Collection([$reordered]));
            $this->fail('The duplicate lead should have been rejected.');
        } catch (ValidationException $exception) {
            $errors = $exception->errors();

            $this->assertArrayHasKey('csv_rows', $errors);
            $this->assertArrayNotHasKey('file', $errors);
        }
    }

    private function row(string $name, string $mobile): Collection
    {
        return new Collection([
            'company_name' => 'Example Company',
            'gst' => '',
            'contact_person_name' => $name,
            'email' => '',
            'mobile' => $mobile,
            'alternate_number' => '',
            'address' => '',
            'remarks' => 'Imported lead',
            'service_product' => 'Consulting',
            'lead_source' => 'Website',
            'employee' => 'Sales User',
        ]);
    }
}
