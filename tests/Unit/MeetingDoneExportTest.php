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
            'next_followup_date' => '30-08-2026 10:00 AM',
            'created_at' => '2026-08-29 14:30:00',
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
            '30-08-2026 10:00 AM',
            '29-08-2026 14:30',
        ], $export->map($history));
    }
}
