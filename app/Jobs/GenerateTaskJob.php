<?php

namespace App\Jobs;

use App\Sale;
use App\SaleDetail;
use App\Item;
use App\ItemDuration;
use App\Telemarketing;
use App\TelemarketingDetail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
class GenerateTaskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transaction;

    /**
     * Create a new job instance.
     */
    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $transaction = $this->transaction;

        $record = Sale::where('id', $transaction->sale_id)->firstOrFail();
        
        // TASK
        $item_name = Item::where('id', $transaction->item_id)->firstOrFail();
        $item = ItemDuration::where('item_id', $transaction->item_id)->firstOrFail();
        
        if ($transaction->description == 'BRANDNEW') {
            $duration_month = $item->brandnew;
        } elseif ($transaction->description == 'REFILL') {
            $duration_month = $item->refill;
        } else {
            $duration_month = $item->for_warranty;
        }

        $telemarketing = Telemarketing::where('company_id', $record->company_id)->first();
        $telemarketingId = $telemarketing->id ?? 1;
        $datePurchased = Carbon::parse($record->date_purchased);
        $followUpDate = $datePurchased->addMonths($duration_month);
        $resultDate = $followUpDate->toDateString();

        $task = [
            'telemarketing_id' => $telemarketingId,
            'order_id' => $transaction->id,
            'date' => $resultDate,
            'task' => 'FOLLOW UP CLIENT',
            'description' => 'CUSTOMER ORDERED ' . $item_name->item_name . ' (' . $transaction->description . ') last ' . $record->date_purchased,
            'assigned_to' => 1,
            'status' => 'TO DO',
            'remarks' => '',
        ];

        TelemarketingDetail::create($task);
    }
}
