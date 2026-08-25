<?php

namespace Tests\Unit;

use App\Models\LeadMaster;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LeadMasterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        app('db')->purge('sqlite');

        Schema::create('lead_master', function (Blueprint $table) {
            $table->increments('lead_id');
            $table->unsignedInteger('iCustomerId');
            $table->string('company_name')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->boolean('isDelete')->default(false);
        });
    }

    public function test_it_allows_multiple_active_leads_for_the_same_company_when_contacts_differ(): void
    {
        LeadMaster::create([
            'iCustomerId' => 10,
            'company_name' => 'Acme Limited',
            'customer_name' => 'Jane Doe',
            'mobile' => '9876543210',
        ]);

        $lead = LeadMaster::create([
            'iCustomerId' => 10,
            'company_name' => '  ACME LIMITED  ',
            'customer_name' => 'John Doe',
            'mobile' => '9876543210',
        ]);

        $this->assertNotNull($lead->lead_id);
    }

    public function test_it_allows_a_new_lead_after_the_active_lead_is_removed(): void
    {
        $lead = LeadMaster::create([
            'iCustomerId' => 10,
            'company_name' => 'Acme Limited',
            'customer_name' => 'Jane Doe',
            'mobile' => '9876543210',
        ]);

        $lead->delete();

        $newLead = LeadMaster::create([
            'iCustomerId' => 10,
            'company_name' => 'Acme Limited',
            'customer_name' => 'Jane Doe',
            'mobile' => '9876543210',
        ]);

        $this->assertDatabaseCount('lead_master', 1);
        $this->assertNotNull($newLead->lead_id);
    }

    /**
     * @dataProvider matchingContactDetails
     */
    public function test_it_rejects_an_active_lead_for_the_same_contact_when_mobile_or_email_matches(array $newLead): void
    {
        LeadMaster::create([
            'iCustomerId' => 10,
            'company_name' => 'First Company',
            'customer_name' => 'Jane Doe',
            'mobile' => '+91 98765-43210',
            'email' => 'Jane@example.com',
        ]);

        $this->expectException(ValidationException::class);

        LeadMaster::create(array_merge([
            'iCustomerId' => 10,
            'company_name' => 'Different Company',
            'customer_name' => '  JANE DOE ',
        ], $newLead));
    }

    public function matchingContactDetails(): array
    {
        return [
            'matching mobile' => [['mobile' => '919876543210', 'email' => 'other@example.com']],
            'matching email' => [['mobile' => '1111111111', 'email' => ' jane@EXAMPLE.COM ']],
        ];
    }

    public function test_it_allows_matching_contact_details_when_the_contact_name_is_different(): void
    {
        LeadMaster::create([
            'iCustomerId' => 10,
            'company_name' => 'First Company',
            'customer_name' => 'Jane Doe',
            'mobile' => '9876543210',
            'email' => 'team@example.com',
        ]);

        $lead = LeadMaster::create([
            'iCustomerId' => 10,
            'company_name' => 'Different Company',
            'customer_name' => 'John Doe',
            'mobile' => '9876543210',
            'email' => 'team@example.com',
        ]);

        $this->assertNotNull($lead->lead_id);
    }

    public function test_contact_matches_are_scoped_to_the_account(): void
    {
        LeadMaster::create([
            'iCustomerId' => 10,
            'company_name' => 'Acme Limited',
            'customer_name' => 'Jane Doe',
            'mobile' => '9876543210',
        ]);

        $lead = LeadMaster::create([
            'iCustomerId' => 20,
            'company_name' => 'Acme Limited',
            'customer_name' => 'Jane Doe',
            'mobile' => '9876543210',
        ]);

        $this->assertNotNull($lead->lead_id);
    }
}
