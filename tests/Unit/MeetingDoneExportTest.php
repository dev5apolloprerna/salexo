<?php
 
 namespace Tests\Unit;
 
 use App\Exports\MeetingDoneExport;
 use Illuminate\Support\Collection;
 use PHPUnit\Framework\TestCase;
 
 class MeetingDoneExportTest extends TestCase
 {
     public function test_it_exports_meeting_done_rows_with_headings(): void
     {
         $history = (object) [
             'customer_name' => 'Test Customer',
             'company_name' => 'Test Company',
             'mobile' => '9999999999',
             'pipeline_name' => 'Meeting Done',
             'Comments' => 'Completed',
             'amount' => '2500',
             'followup_by_name' => 'Test User',
            'created_by_name' => 'Test Creator',
         ];
 
         $export = new MeetingDoneExport(new Collection([$history]));
 
         $this->assertSame([$history], $export->collection()->all());
         $this->assertSame('Customer Name', $export->headings()[0]);
         $this->assertSame([
             'Test Customer',
             'Test Company',
             '9999999999',
             'Meeting Done',
             'Completed',
             '2500',
             'Test User',
            'Test Creator',
         ], $export->map($history));
        $this->assertSame('Created By', $export->headings()[7]);
    }

    public function test_it_removes_amount_heading_when_filtered_rows_have_no_amount(): void
    {
        $history = (object) [
            'customer_name' => 'Test Customer',
            'company_name' => 'Test Company',
            'mobile' => '9999999999',
            'pipeline_name' => 'Meeting Done',
            'Comments' => 'Completed',
            'amount' => '0',
            'followup_by_name' => 'Test User',
            'created_by_name' => 'Test Creator',
        ];

        $export = new MeetingDoneExport(new Collection([$history]));

        $this->assertNotContains('Amount', $export->headings());
        $this->assertCount(7, $export->map($history));
     }
 }
