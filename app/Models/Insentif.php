<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insentif extends Model
{
    use HasFactory;

    protected $fillable = ['access_id', 'sales_qty', 'intensive', 'type', 'period'];

    public function access()
    {
        return $this->belongsTo(Access::class, 'access_id');
    }

    // Hanya bisa untuk ambil data insentive periode daily dan weekly
    public static function detailFor($access_id, $salesData)
    {
        $insentiveQuery = self::where('access_id', $access_id)
            ->orderBy('sales_qty', 'asc');

        $dailyInsentive = clone $insentiveQuery;
        $dailyInsentive = $dailyInsentive->where('period', 'daily')->get(['sales_qty', 'insentive', 'type', 'period']);

        $weeklyInsentive = clone $insentiveQuery;
        $weeklyInsentive = $weeklyInsentive->where('period', 'weekly')->get(['sales_qty', 'insentive', 'type', 'period']);

        $totalQty = 0;
        $totalDailyInsentive = 0;
        $insentives = [];
        foreach ($salesData as $i => $sale) {
            // Hitung total untuk tentukan mingguan
            $totalQty += $sale->total_qty;

            $insentive = self::getInsentive($sale->total_qty, $dailyInsentive);
            $totalDailyInsentive += $insentive;
            $salesData[$i]->insentive = $insentive;
            $insentives[] = $salesData[$i];
        }
        $weeklyInsentive = self::getInsentive($totalQty, $weeklyInsentive);
        return (object)[
            'total_insentive' => $weeklyInsentive + $totalDailyInsentive,
            'total_sale_qty' => $totalQty,
            'total_weekly_insentive' => $weeklyInsentive,
            'total_daily_insentive' => $totalDailyInsentive,
            'detail' => $insentives,
        ];
    }

    public static function getInsentive($sales_qty, $insentifs)
    {
        $selectedIncentive = 0;

        foreach ($insentifs as $item) {
            if ($sales_qty >= $item['sales_qty']) {
                $selectedIncentive = $item['insentive'];
            } else {
                break;
            }
        }

        return $selectedIncentive;
    }
}
