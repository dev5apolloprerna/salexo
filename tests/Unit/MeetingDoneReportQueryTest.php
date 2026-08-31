<?php

namespace Tests\Unit;

use App\Http\Controllers\Company\ReportController;
use ReflectionMethod;
use Tests\TestCase;

class MeetingDoneReportQueryTest extends TestCase
{
    public function test_contact_details_fall_back_to_archived_leads(): void
    {
        $method = new ReflectionMethod(ReportController::class, 'meetingDoneQuery');
        $query = $method->invoke(new ReportController(), 32);
        $sql = $query->toSql();

        $this->assertStringContainsString('left join `lead_master`', $sql);
        $this->assertStringContainsString('left join `deal_done`', $sql);
        $this->assertStringContainsString('left join `deal_cancel`', $sql);
        $this->assertStringContainsString('left join `employee_master` as `created_by_employee`', $sql);
        $this->assertStringContainsString('left join `lead_pipeline_master` as `current_pipeline`', $sql);
        $this->assertStringContainsString('COALESCE(lead_master.status, deal_done.status, deal_cancel.status)', $sql);
        $this->assertStringContainsString('`lead_master`.`isDelete` = ?', $sql);
        $this->assertStringContainsString('`deal_done`.`isDelete` = ?', $sql);
        $this->assertStringContainsString('`deal_cancel`.`isDelete` = ?', $sql);
        $this->assertStringContainsString('`lead_history`.`isDelete` = ?', $sql);
        $this->assertStringContainsString('`lead_master`.`lead_id` is not null', $sql);
        $this->assertStringContainsString(
            "COALESCE(NULLIF(TRIM(lead_master.customer_name), ''), NULLIF(TRIM(deal_done.customer_name), ''), NULLIF(TRIM(deal_cancel.customer_name), '')) as customer_name",
            $sql
        );
        $this->assertSame([32, 0, 32, 0, 32, 0, 32, 32, 0], $query->getBindings());
    }
}
