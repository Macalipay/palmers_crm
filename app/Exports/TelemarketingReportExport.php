<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TelemarketingReportExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    protected $query;
    protected $rowNumber = 0;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            '#',
            'Company',
            'PO/FO No',
            'Follow Up Date',
            'Status',
            'Assigned To',
            'Contact Person',
            'Contact No',
            'Date Purchased',
            'Total Amount',
            'Remarks',
        ];
    }

    public function map($record): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            optional(optional($record->telemarketing)->company)->company_name ?: '--',
            optional(optional($record->csd)->sale)->po_no ?: '--',
            $this->formatDate($record->date),
            $record->status ?: '--',
            optional($record->user)->name ?: 'UNASSIGNED',
            optional(optional($record->telemarketing)->company)->contact_person ?: '--',
            optional(optional($record->telemarketing)->company)->contact_no ?: '--',
            $this->formatDate(optional(optional($record->csd)->sale)->date_purchased),
            is_null($record->total_amount) ? 0 : (float) $record->total_amount,
            $record->remarks ?: '--',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    private function formatDate($value)
    {
        if (empty($value)) {
            return '--';
        }

        try {
            return Carbon::parse($value)->format('M d, Y');
        } catch (\Exception $e) {
            return '--';
        }
    }
}
