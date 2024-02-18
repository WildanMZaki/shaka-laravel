<?php

namespace App\Helpers;

use App\Models\Expenditure;
use App\Models\Presence;
use App\Models\Product;
use App\Models\Restock;
use App\Models\Sale;
use App\Models\User;
use App\Models\WeeklySallary;
use DateTime;
use Exception;

/**
 * Just a litle not, Muwiza is stand for my name: Wildan Muhammad Zaki, Mu-wi-za: hehe
 */
class Fun
{
    use Formatter;

    public static $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu'];
    // Dapatkan total Karyawan & Kehadirannya
    public static function presenceEmployee()
    {
        $totEmployees = User::where('access_id', '>', 2)->count();
        $totPresense = Presence::where('flag', 'hadir')
            ->whereDate('date', now())
            ->count();
        $totPermit = Presence::whereIn('flag', ['izin', 'sakit'])
            ->whereDate('date', now())
            ->count();

        return (object)[
            'total_karyawan' => $totEmployees,
            'total_hadir' => $totPresense,
            'total_izin' => $totPermit,
        ];
    }

    public static function getProducts()
    {
        // Untuk saat ini belum ada rentang
        $coming = Restock::sum('qty');
        $remaining = 0;
        foreach (Product::get() as $product) {
            $remaining += $product->stock;
        }
        $sold = Sale::whereDate('created_at', now())->sum('qty');

        return (object) [
            'in' => $coming,
            'in_formatted' => self::ribuan($coming),
            'remaining' => $remaining,
            'remaining_formatted' => self::ribuan($remaining),
            'sold' => $sold,
            'sold_formatted' => self::ribuan($sold),
        ];
    }

    public static function getProfitDebug($period = '-14 days')
    {
        $today = date('Y-m-d 23:59:59');
        $twoWeeksAgo = date('Y-m-d 00:00:00', strtotime($period, strtotime($today)));
        $totalIncome = Sale::whereBetween('created_at', [$twoWeeksAgo, $today])->sum('total');
        $totalModal = Sale::whereBetween('created_at', [$twoWeeksAgo, $today])->sum('modal');
        $totalExpenditure = self::nominalRupiah(self::getExpenditures());
        $totalProfit = $totalIncome - $totalModal;
        $totalProfit -= $totalExpenditure;
        return [
            'income' => $totalIncome, 'modal' => $totalModal, 'expen' => $totalExpenditure, 'profit' => $totalProfit,
        ];
    }
    public static function getProfit($period = '-14 days')
    {
        $today = date('Y-m-d 23:59:59');
        $twoWeeksAgo = date('Y-m-d 00:00:00', strtotime($period, strtotime($today)));
        $totalIncome = Sale::whereBetween('created_at', [$twoWeeksAgo, $today])->sum('total');
        $totalModal = Sale::whereBetween('created_at', [$twoWeeksAgo, $today])->sum('modal');
        $totalExpenditures = self::nominalRupiah(self::getExpenditures($period));
        $totalProfit = $totalIncome - $totalModal;
        $totalProfit -= $totalExpenditures;
        $totalGivenSallaries = self::getGivenSallaries($period);
        $totalProfit -= $totalGivenSallaries;
        return self::rupiah($totalProfit);
    }
    public static function getIncome($period = '-14 days')
    {
        $today = date('Y-m-d 23:59:59');
        $twoWeeksAgo = date('Y-m-d 00:00:00', strtotime($period, strtotime($today)));
        $totalIncome = Sale::whereBetween('created_at', [$twoWeeksAgo, $today])->sum('total');
        return self::rupiah($totalIncome);
    }
    public static function getExpenditures($period = '-14 days')
    {
        $today = date('Y-m-d');
        $twoWeeksAgo = date('Y-m-d', strtotime($period, strtotime($today)));
        $totalExpenditure = Expenditure::whereBetween('created_at', [$twoWeeksAgo, $today])->sum('nominal');
        return self::rupiah($totalExpenditure);
    }
    public static function getGivenSallaries($period = '-14 days')
    {
        $today = date('Y-m-d');
        $twoWeeksAgo = date('Y-m-d', strtotime($period, strtotime($today)));
        $totalGivenSallary = WeeklySallary::whereBetween('created_at', [$twoWeeksAgo, $today])->sum('total');
        return $totalGivenSallary;
    }

    public static function periodDates(?string $mondayDate = null, $lastDay = 'Sabtu')
    {
        if (!in_array($lastDay, self::$days)) throw new Exception("Hari '$lastDay' tidak diizinkan");
        $dates = [];

        $mondayThisWeek = self::firstMonday();
        $mondayDateTime = new DateTime($mondayDate ?? $mondayThisWeek);

        for ($i = 0; $i < array_search($lastDay, self::$days); $i++) {
            $dates[] = $mondayDateTime->format('Y-m-d');
            $mondayDateTime->modify('+1 day');
        }

        return $dates;
    }

    public static function period(?string $mondayDate = null, $lastDay = 'Sabtu')
    {
        if (!in_array($lastDay, self::$days)) throw new Exception("Hari '$lastDay' tidak diizinkan");
        $periodDates = self::periodDates($mondayDate, $lastDay);
        $start = reset($periodDates);
        $end = end($periodDates);
        return [$start, $end];
    }
    public static function periodWithHour(?string $mondayDate = null, $lastDay = 'Sabtu')
    {
        if (!in_array($lastDay, self::$days)) throw new Exception("Hari '$lastDay' tidak diizinkan");
        $periodDates = self::periodDates($mondayDate, $lastDay);
        $start = reset($periodDates) . ' 00:00:00';
        $end = end($periodDates) . ' 23:59:59';
        return [$start, $end];
    }
}
