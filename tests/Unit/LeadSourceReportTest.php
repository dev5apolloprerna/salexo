<?php

namespace Tests\Unit;

use App\Support\LeadSourceReport;
use PHPUnit\Framework\TestCase;
use stdClass;

class LeadSourceReportTest extends TestCase
{
    public function test_it_combines_counts_for_duplicate_source_names(): void
    {
        $sources = collect([
            $this->source(10, 'Facebook'),
            $this->source(11, ' facebook '),
            $this->source(20, 'IndiaMart'),
        ]);
        $counts = collect([
            10 => collect([1 => 1, 2 => 2]),
            11 => collect([1 => 1, 2 => 3]),
            20 => collect([1 => 2]),
        ]);

        [$consolidatedSources, $consolidatedCounts] = LeadSourceReport::consolidate($sources, $counts);

        $this->assertCount(2, $consolidatedSources);
        $this->assertSame([1 => 2, 2 => 5], $consolidatedCounts->get(10)->all());
        $this->assertSame([1 => 2], $consolidatedCounts->get(20)->all());
    }

    private function source(int $id, string $name): stdClass
    {
        return (object) [
            'lead_source_id' => $id,
            'lead_source_name' => $name,
        ];
    }
}
