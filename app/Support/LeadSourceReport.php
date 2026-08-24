<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class LeadSourceReport
{
    /**
     * Merge duplicate lead-source master rows (including case/spacing variants).
     *
     * Leads can reference different master IDs that display as the same source.
     * Reports should present one source and add the counts from every matching ID.
     *
     * @return array{0: Collection, 1: Collection}
     */
    public static function consolidate(Collection $sources, Collection $statusCounts): array
    {
        $consolidatedSources = collect();
        $consolidatedCounts = collect();

        $sources->groupBy(function ($source) {
            return Str::lower(trim((string) $source->lead_source_name));
        })->each(function (Collection $matchingSources) use ($statusCounts, $consolidatedSources, $consolidatedCounts) {
            $canonicalSource = $matchingSources->first();
            $totals = collect();

            foreach ($matchingSources as $source) {
                foreach ($statusCounts->get($source->lead_source_id, collect()) as $status => $count) {
                    $totals->put($status, (int) $totals->get($status, 0) + (int) $count);
                }
            }

            $consolidatedSources->push($canonicalSource);
            $consolidatedCounts->put($canonicalSource->lead_source_id, $totals);
        });

        return [$consolidatedSources->values(), $consolidatedCounts];
    }
}
