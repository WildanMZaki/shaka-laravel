<?php

namespace App\Models;

use App\Helpers\Fun;
use App\Helpers\Muwiza;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kasbon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'nominal', 'note', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public static function of($user_id, $withHistory = false): int|object
    {
        $now = Carbon::now();
        $startOfWeek = $now->startOfWeek(Carbon::MONDAY);

        $kasbons = self::where('user_id', $user_id)
            ->where('created_at', '>=', $startOfWeek)
            ->get();
        $totalThisWeek = $kasbons->sum('nominal');
        $limitThisWeek = Settings::of('Limit Kasbon');
        $left = $limitThisWeek - $totalThisWeek;
        if ($withHistory) {
            return (object)[
                'totalThisWeek' => $totalThisWeek,
                'left' => $left,
                'history' => $kasbons,
            ];
        }
        return $left;
    }

    public static function keepFrom($user_id, ?string $dateTime = null)
    {
        $period = Fun::periodWithHour(Muwiza::firstMonday($dateTime));
        $hargaDefault = Settings::of('Default Harga Jual');

        $keepData = self::where('user_id', $user_id)
            ->where('type', 'keep')
            ->whereBetween('created_at', $period)
            ->selectRaw("DATE(created_at) as date, SUM(nominal) as nominal_keep, FLOOR(SUM(nominal) / $hargaDefault) as qty_keep")
            ->groupBy('date')
            ->get();

        return $keepData;
    }

    public static function kasbonFrom($user_id, ?string $dateTime = null)
    {
        $period = Fun::periodWithHour(Muwiza::firstMonday($dateTime));

        $totalKasbon = self::where('user_id', $user_id)
            ->where('type', 'kasbon')
            ->whereBetween('created_at', $period)
            ->sum('nominal');

        return (int)$totalKasbon;
    }

    public static function updateKeepStatusThisWeekFrom($user_id, ?string $dateTime = null)
    {
        $keepData = Kasbon::keepFrom($user_id, $dateTime);

        $rangeDate = Fun::periodWithHour(Muwiza::firstMonday($dateTime));
        $kasbons = Kasbon::where('user_id', $user_id)
            ->where('type', 'keep')
            ->whereIn('status', ['approved', 'unpaid'])
            ->whereBetween('created_at', $rangeDate)
            ->get();

        $firstKeep = $kasbons->sortBy('created_at')->first();
        if ($firstKeep == null) {
            return (object)[
                'nominal_lebih' => 0,
                'unpaid_keep' => 0,
            ];
        }
        $firstKeepDate = $firstKeep->created_at;
        $firstKeepTime = strtotime(date($firstKeepDate));

        $salesData = Sale::fromSPG($user_id);

        $target = Settings::of('Target Jual Harian SPG Freelancer');
        $defaultSalePrice = Settings::of('Default Harga Jual');

        // Qty yang lebih dari target:
        $qtyPass = 0;
        foreach ($salesData as $i => $sale) {
            $keepQty = self::getQtyKeepByDate($keepData, $sale->date);
            $realQty = $sale->total_qty - $keepQty;
            $salesData[$i]->realQty = $realQty;
            $salesData[$i]->keep = $keepQty * $defaultSalePrice;
            if ($realQty > $target && strtotime(date($sale->date)) > $firstKeepTime) {
                $qtyPass += ($realQty - $target);
            }
        }

        $nominalLebih = $qtyPass * $defaultSalePrice;

        $kasbonUnpaid = 0;
        foreach ($kasbons as $kasbon) {
            if ($kasbon->status != 'paid') {
                if ($nominalLebih >= $kasbon->nominal) {
                    $kasbon->status = 'paid';
                    $kasbon->save();

                    $nominalLebih -= $kasbon->nominal;
                } else {
                    $kasbonUnpaid += $kasbon->nominal;

                    $kasbon->status = 'unpaid';
                    $kasbon->save();
                }
            }
        }

        return (object)[
            'nominal_lebih' => $nominalLebih,
            'unpaid_keep' => $kasbonUnpaid - $nominalLebih,
        ];
    }

    public static function getQtyKeepByDate($data, $date)
    {
        foreach ($data as $item) {
            if ($item['date'] === $date) {
                return $item['qty_keep'];
            }
        }
        // Return 0 if no match is found
        return 0;
    }
}
