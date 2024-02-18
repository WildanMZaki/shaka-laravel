<?php

namespace App\Jobs;

use App\Helpers\Muwiza;
use App\Models\Kasbon;
use App\Models\Sale;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessSalesData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    /**
     * Create a new job instance.
     *
     * @param  int  $userId
     * @return void
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $keepData = Kasbon::keepFrom($this->userId, date('Y-m-d'));

        $rangeDate = Muwiza::mondayUntilNow();
        $kasbons = Kasbon::where('user_id', $this->userId)
            ->where('type', 'keep')
            ->whereIn('status', ['unpaid', 'approved'])
            ->whereBetween('created_at', $rangeDate)
            ->get();

        $firstKeepDate = $kasbons->sortBy('created_at')->first()->created_at;
        $firstKeepTime = strtotime(date($firstKeepDate));

        $salesData = Sale::fromSPG($this->userId);

        $target = Settings::of('Target Jual Harian SPG Freelancer');
        $defaultSalePrice = Settings::of('Default Harga Jual');

        // Qty yang lebih dari target:
        $qtyPass = 0;
        foreach ($salesData as $i => $sale) {
            $keepQty = Kasbon::getQtyKeepByDate($keepData, $sale->date);
            $realQty = $sale->total_qty - $keepQty;
            $salesData[$i]->realQty = $realQty;
            $salesData[$i]->keep = $keepQty * $defaultSalePrice;
            if ($realQty > $target && strtotime(date($sale->date)) > $firstKeepTime) {
                $qtyPass += ($realQty - $target);
            }
        }

        $nominalLebih = $qtyPass * $defaultSalePrice;

        $kasbonPaid = Kasbon::where('user_id', $this->userId)->where('status', 'paid')->where('type', 'keep')->sum('nominal');
        $nominalLebih -= $kasbonPaid;

        foreach ($kasbons as $kasbon) {
            if ($kasbon->status != 'paid') {
                if ($nominalLebih >= $kasbon->nominal) {
                    $kasbon->status = 'paid';
                    $kasbon->save();

                    $nominalLebih -= $kasbon->nominal;
                }
            }
        }
    }
}
