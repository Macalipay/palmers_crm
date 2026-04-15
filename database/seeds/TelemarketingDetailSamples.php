<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TelemarketingDetailSamples extends Seeder
{
    /**
     * Seed a few reusable telemarketing detail sample rows
     * that stay visible on the telemarketing screen.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        $sampleRows = [
            [
                'sale_detail_id' => 1,
                'task' => 'APRIL SAMPLE FOLLOW UP A',
                'status' => 'TO DO',
                'remarks' => 'Sample telemarketing detail for UI testing.',
                'call_duration' => '6',
                'new_order_id' => null,
                'total_amount' => null,
                'date' => $now->copy()->addDay()->toDateString(),
            ],
            [
                'sale_detail_id' => 2,
                'task' => 'APRIL SAMPLE FOLLOW UP B',
                'status' => 'ON-HOLD',
                'remarks' => 'Sample telemarketing detail marked inactive.',
                'call_duration' => '2',
                'new_order_id' => null,
                'total_amount' => null,
                'date' => $now->copy()->addDays(2)->toDateString(),
            ],
            [
                'sale_detail_id' => 3,
                'task' => 'APRIL SAMPLE FOLLOW UP C',
                'status' => 'COMPLETED',
                'remarks' => 'Sample completed telemarketing detail.',
                'call_duration' => '9',
                'new_order_id' => 'TM-SAMPLE-0003',
                'total_amount' => 6400,
                'date' => $now->copy()->toDateString(),
            ],
        ];

        foreach ($sampleRows as $row) {
            $saleDetail = DB::table('sale_details')->where('id', $row['sale_detail_id'])->first();

            if (!$saleDetail) {
                continue;
            }

            $sale = DB::table('sales')->where('id', $saleDetail->sale_id)->first();

            if (!$sale) {
                continue;
            }

            $telemarketing = DB::table('telemarketings')->where('company_id', $sale->company_id)->first();

            if (!$telemarketing) {
                continue;
            }

            $existing = DB::table('telemarketing_details')
                ->where('task', $row['task'])
                ->where('order_id', $saleDetail->id)
                ->first();

            $payload = [
                'telemarketing_id' => $telemarketing->id,
                'date' => $row['date'],
                'task' => $row['task'],
                'description' => 'CUSTOMER ORDERED sample follow-up for sale #' . $sale->id,
                'assigned_to' => 1,
                'status' => $row['status'],
                'remarks' => $row['remarks'],
                'active' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'lead_status' => $telemarketing->lead_status ?: 'PROSPECT',
                'branch_id' => 2,
                'order_id' => $saleDetail->id,
                'total_amount' => $row['total_amount'],
                'new_order_id' => $row['new_order_id'],
                'call_duration' => $row['call_duration'],
                'assigned_date' => $now->copy()->subDay()->toDateString(),
            ];

            if ($existing) {
                DB::table('telemarketing_details')
                    ->where('id', $existing->id)
                    ->update($payload);
                continue;
            }

            DB::table('telemarketing_details')->insert($payload);
        }
    }
}
