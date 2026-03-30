<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TelemarketingReportSamples extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        $sampleItems = [
            [
                'item_name' => 'SAMPLE TM FIRE ALARM PANEL',
                'description' => 'Sample CSD item for telemarketing detail and report testing.',
                'amount' => 12500,
            ],
            [
                'item_name' => 'SAMPLE TM FIRE EXTINGUISHER SET',
                'description' => 'Sample CSD item for telemarketing detail and report testing.',
                'amount' => 8500,
            ],
            [
                'item_name' => 'SAMPLE TM EMERGENCY LIGHT KIT',
                'description' => 'Sample CSD item for telemarketing detail and report testing.',
                'amount' => 6400,
            ],
        ];

        $itemIds = [];
        foreach ($sampleItems as $item) {
            $existingItem = DB::table('items')->where('item_name', $item['item_name'])->first();

            if ($existingItem) {
                DB::table('items')
                    ->where('id', $existingItem->id)
                    ->update([
                        'description' => $item['description'],
                        'amount' => $item['amount'],
                        'branch_id' => 2,
                        'division_id' => 1,
                        'updated_by' => 1,
                        'updated_at' => $now,
                    ]);

                $itemIds[] = $existingItem->id;
                continue;
            }

            $itemIds[] = DB::table('items')->insertGetId([
                'item_name' => $item['item_name'],
                'description' => $item['description'],
                'amount' => $item['amount'],
                'active' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'division_id' => 1,
                'branch_id' => 2,
            ]);
        }

        $sales = DB::table('sales')
            ->whereIn('id', [4, 5, 7])
            ->orderBy('id')
            ->get();

        foreach ($sales as $index => $sale) {
            $telemarketing = DB::table('telemarketings')
                ->where('company_id', $sale->company_id)
                ->first();

            if (!$telemarketing) {
                continue;
            }

            $saleDetailSerial = 'TM-SAMPLE-SD-' . $sale->id;
            $saleDetail = DB::table('sale_details')
                ->where('serial_no', $saleDetailSerial)
                ->first();

            $amount = $sampleItems[$index]['amount'];
            $quantity = 1;
            $discount = 0;
            $total = $amount * $quantity;

            if (!$saleDetail) {
                $saleDetailId = DB::table('sale_details')->insertGetId([
                    'sale_id' => $sale->id,
                    'item_id' => $itemIds[$index],
                    'brand_id' => null,
                    'warranty_no' => 'TM-WAR-' . $sale->id,
                    'serial_no' => $saleDetailSerial,
                    'quantity' => $quantity,
                    'amount' => $amount,
                    'discount' => $discount,
                    'total' => $total,
                    'active' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'deleted_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'description' => 'BRANDNEW',
                ]);
            } else {
                $saleDetailId = $saleDetail->id;
            }

            $task = 'SAMPLE TELEMARKETING FOLLOW UP ' . $sale->id;
            $detail = DB::table('telemarketing_details')
                ->where('task', $task)
                ->where('order_id', $saleDetailId)
                ->first();

            $statuses = ['TO DO', 'IN PROGRESS', 'PENDING'];
            $detailStatus = $statuses[$index];

            if (!$detail) {
                $detailId = DB::table('telemarketing_details')->insertGetId([
                    'telemarketing_id' => $telemarketing->id,
                    'date' => Carbon::now()->addDays($index + 1)->toDateString(),
                    'task' => $task,
                    'description' => 'CUSTOMER ORDERED sample follow-up record for report testing on sale #' . $sale->id,
                    'assigned_to' => 1,
                    'status' => $detailStatus,
                    'remarks' => 'Sample telemarketing detail seeded for report workflow testing.',
                    'active' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'deleted_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'lead_status' => 'ACQUIRE',
                    'branch_id' => 2,
                    'order_id' => $saleDetailId,
                    'total_amount' => $total,
                    'new_order_id' => 'SAMPLE-PO-' . $sale->id,
                    'call_duration' => (string) (($index + 1) * 5),
                    'assigned_date' => Carbon::now()->subDays(1)->toDateString(),
                ]);
            } else {
                $detailId = $detail->id;
            }

            $reportRemarks = 'Sample telemarketing issue report for sale #' . $sale->id;
            $report = DB::table('telemarketing_detail_reports')
                ->where('telemarketing_detail_id', $detailId)
                ->where('remarks', $reportRemarks)
                ->first();

            if (!$report) {
                $reportId = DB::table('telemarketing_detail_reports')->insertGetId([
                    'telemarketing_detail_id' => $detailId,
                    'reported_by' => 1,
                    'remarks' => $reportRemarks,
                    'status' => $index === 1 ? 'RESOLVED' : 'OPEN',
                    'resolution_remarks' => $index === 1 ? 'Sample resolution completed and verified.' : null,
                    'resolved_by' => $index === 1 ? 1 : null,
                    'resolved_at' => $index === 1 ? Carbon::now()->subHours(2) : null,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                $reportId = $report->id;
            }

            DB::table('telemarketing_call_logs')->updateOrInsert(
                [
                    'telemarketing_detail_id' => $detailId,
                    'status' => $detailStatus,
                    'remarks' => 'Sample call log for seeded telemarketing detail #' . $detailId,
                ],
                [
                    'sale_id' => $sale->id,
                    'new_order_id' => 'SAMPLE-PO-' . $sale->id,
                    'total_amount' => $total,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
