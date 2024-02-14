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
        $user = User::find($this->userId);
        if ($user->access_id != 6) {
            return;
        }

        $rangeDate = Muwiza::mondayUntilNow();
        $kasbons = Kasbon::where('user_id', $this->userId)
            ->where('type', 'keep')
            ->where('status', 'approved')
            ->whereBetween('created_at', $rangeDate)
            ->get();

        // If there are no kasbons, stop the job
        if ($kasbons->isEmpty()) {
            return;
        }

        $firstKasbonDate = $kasbons->sortBy('created_at')->first()->created_at;

        $salesData = Sale::where('user_id', $this->userId)
            ->where('created_at', '>', $firstKasbonDate)
            ->where('status', 'done')
            ->selectRaw('DATE(created_at) as date, SUM(qty) as total_qty, SUM(total) as total_income')
            ->groupBy('date')
            ->get();

        $target = Settings::of('Target Jual Harian SPG Freelancer');

        // Qty yang lebih dari target:
        $qtyPass = 0;
        foreach ($salesData as $sale) {
            if ($sale->total_qty > $target) {
                $qtyPass += ($sale->total_qty - $target);
            }
        }

        $defaultSalePrice = Settings::of('Default Harga Jual');
        $nominalLebih = $qtyPass * $defaultSalePrice;

        $kasbonPaid = Kasbon::where('user_id', $this->userId)->where('status', 'paid')->where('type', 'keep')->sum('nominal');
        $nominalLebih -= $kasbonPaid;

        foreach ($kasbons as $kasbon) {
            if ($nominalLebih >= $kasbon->nominal) {
                $kasbon->status = 'paid';
                $kasbon->save();

                $nominalLebih -= $kasbon->nominal;
            }
        }
    }
}
