<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MeetingDoneExport implements FromCollection, WithHeadings, WithMapping
{
    private $leadHistory;

    public function __construct(Collection $leadHistory)
    {
        $this->leadHistory = $leadHistory;
    }

    public function collection(): Collection
    {
        return $this->leadHistory;
    }

    public function headings(): array
    {
        return [
            'Customer Name',
            'Company',
            'Mobile',
            'Status',
            'Comments',
            'Amount',
            'Follow Up By',
            'Next Follow-up Date',
            'Date',
        ];
    }

    public function map($history): array
    {
        return [
            $history->customer_name ?? '-',
            $history->company_name ?? '-',
            $history->mobile ?? '-',
            $history->pipeline_name ?? '-',
            $history->Comments ?? '-',
            $history->amount && $history->amount != '0' ? $history->amount : '-',
            $history->followup_by_name ?? '-',
            $history->next_followup_date ?? '-',
            $history->created_at ? Carbon::parse($history->created_at)->format('d-m-Y H:i') : '-',
        ];
    }
}
    