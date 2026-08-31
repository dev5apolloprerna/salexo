<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MeetingDoneExport implements FromCollection, WithHeadings, WithMapping
{
    private $leadHistory;
    private $showAmount;

    public function __construct(Collection $leadHistory)
    {
        $this->leadHistory = $leadHistory;
        $this->showAmount = $leadHistory->contains(function ($history) {
            $amount = $history->amount ?? null;

            return $amount !== null && $amount !== '' && (float) $amount != 0;
        });
    }

    public function collection(): Collection
    {
        return $this->leadHistory;

    }

    public function headings(): array
    {
        $headings = [
            'Customer Name',
            'Company',
            'Mobile',
            'Status',
            'Comments',
            // 'Amount',
            'Follow Up By',
            /*'Next Follow-up Date',
            'Date',*/
            'Created By',
        ];
         if ($this->showAmount) {
            array_splice($headings, 5, 0, ['Amount']);
        }

        return $headings;
    }

    public function map($history): array
    {
        $row = [
            $history->customer_name ?? '-',
            $history->company_name ?? '-',
            $history->mobile ?? '-',
            $history->pipeline_name ?? '-',
            $history->current_status_name ?? '-',
            $history->Comments ?? '-',
            // $history->amount && $history->amount != '0' ? $history->amount : '-',
            $history->followup_by_name ?? '-',
/*            $history->next_followup_date ?? '-',
            $history->created_at ? Carbon::parse($history->created_at)->format('d-m-Y H:i') : '-',*/
            $history->created_by_name ?? '-',
        ];
    if ($this->showAmount) {
            $amount = $history->amount ?? null;
            array_splice($row, 5, 0, [$amount && $amount != '0' ? $amount : '-']);
        }

        return $row;
    }
}
    