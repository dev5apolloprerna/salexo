<?php

namespace Tests\Unit;

use App\Http\Controllers\Company\CompanyClientHomeController;
use Carbon\Carbon;
use ReflectionMethod;
use Tests\TestCase;

class DashboardTopPerformerPeriodTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_it_uses_the_current_calendar_month_by_default(): void
    {
        Carbon::setTestNow('2026-09-15 12:00:00');

        [$from, $to] = $this->resolvePeriod(null, null);

        $this->assertSame('2026-09-01 00:00:00', $from->format('Y-m-d H:i:s'));
        $this->assertSame('2026-09-30 23:59:59', $to->format('Y-m-d H:i:s'));
    }

    public function test_explicit_dashboard_dates_override_the_month_boundaries(): void
    {
        [$from, $to] = $this->resolvePeriod('2026-07-05', '2026-08-12');

        $this->assertSame('2026-07-05 00:00:00', $from->format('Y-m-d H:i:s'));
        $this->assertSame('2026-08-12 23:59:59', $to->format('Y-m-d H:i:s'));
    }

    public function test_a_single_search_date_uses_its_calendar_month(): void
    {
        [$from, $to] = $this->resolvePeriod(null, '2026-02-10');

        $this->assertSame('2026-02-01 00:00:00', $from->format('Y-m-d H:i:s'));
        $this->assertSame('2026-02-10 23:59:59', $to->format('Y-m-d H:i:s'));
    }

    private function resolvePeriod(?string $fromDate, ?string $toDate): array
    {
        $method = new ReflectionMethod(CompanyClientHomeController::class, 'topPerformerPeriod');
        $method->setAccessible(true);

        return $method->invoke(new CompanyClientHomeController(), $fromDate, $toDate);
    }
}
