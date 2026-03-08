<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReportExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
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
            'Store',
            'Industry',
            'Source',
            'Payment Term',
            'PO/OF No',
            'Date Purchased',
            'Amount',
            'Sales Agent',
            'Sales Associate',
            'Merchandiser',
            'Division',
            'Branch',
            'Agreed Delivery',
            'Actual Delivery',
            'Date Posted',
            'Date Encode',
            'Date Received',
            'Date Filed',
            'Deadline',
            'Project Title',
            'Contact Person',
            'Telephone',
            'Email',
            'Status',
        ];
    }

    public function map($sale): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            optional($sale->company)->company_name ?: '--',
            optional($sale->store)->store_name ?: '--',
            optional($sale->company)->industry ?: '--',
            optional($sale->source)->source ?: '--',
            $sale->payment_term ?: '--',
            $sale->po_no ?: '--',
            $this->formatDate($sale->date_purchased),
            is_null($sale->amount) ? 0 : (float) $sale->amount,
            optional($sale->user)->name ?: '--',
            optional($sale->sales_associate)->sales_associate ?: '--',
            optional($sale->merchandiser)->merchandiser ?: '--',
            optional($sale->division)->division ?: '--',
            optional($sale->branch)->branch_name ?: '--',
            $this->formatDate($sale->agreed_delivery_date),
            $this->formatDate($sale->actual_delivery_date),
            $this->formatDate($sale->date_posted),
            $this->formatDate($sale->date_encode),
            $this->formatDate($sale->date_received),
            $this->formatDate($sale->date_filed),
            $this->formatDate($sale->deadline),
            $sale->project_title ?: '--',
            $sale->contact_person ?: '--',
            $sale->telephone_no ?: '--',
            $sale->email ?: '--',
            ((string) $sale->active === '1') ? 'ACTIVE' : 'INACTIVE',
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
