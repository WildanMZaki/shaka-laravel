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

    public static function getProfit(?array $period = null)
    {
        if (!$period) {
            $period = self::periodWithHour();
        }
        $totalIncome = Sale::whereBetween('created_at', $period)->sum('total');
        $totalModal = Sale::whereBetween('created_at', $period)->sum('modal');
        $totalExpenditures = self::nominalRupiah(self::getExpenditures($period));
        $totalProfit = $totalIncome - $totalModal;
        $totalProfit -= $totalExpenditures;
        $totalGivenSallaries = self::getGivenSallaries($period);
        $totalProfit -= $totalGivenSallaries;
        return self::rupiah($totalProfit);
    }
    public static function getIncome(?array $period = null)
    {
        if (!$period) {
            $period = self::periodWithHour();
        }
        $totalIncome = Sale::whereBetween('created_at', $period)->sum('total');
        return self::rupiah($totalIncome);
    }
    public static function getExpenditures(?array $period = null)
    {
        if (!$period) {
            $period = self::periodWithHour();
        }
        $totalExpenditure = Expenditure::whereBetween('created_at', $period)->sum('nominal');
        return self::rupiah($totalExpenditure);
    }
    public static function getGivenSallaries(?array $period = null)
    {
        if (!$period) {
            $period = self::periodWithHour();
        }
        $totalGivenSallary = WeeklySallary::whereBetween('period_start', $period)->sum('total');
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

    public static function getMondaysInMonth($year, $month)
    {
        // Get the number of days in the given month
        $numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Initialize an empty array to store the Mondays
        $mondays = [];

        // Loop through all the days in the month
        for ($day = 1; $day <= $numDays; $day++) {
            // Get the day of the week (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
            $dayOfWeek = date('w', mktime(0, 0, 0, $month, $day, $year));

            // If the day is Monday (1), add it to the array
            if ($dayOfWeek == 1) {
                // Pad month and day with leading zeros
                $paddedMonth = str_pad($month, 2, '0', STR_PAD_LEFT);
                $paddedDay = str_pad($day, 2, '0', STR_PAD_LEFT);

                // Add to the array
                $mondays[] = "$year-$paddedMonth-$paddedDay";
            }
        }

        return $mondays;
    }

    public static function years($start = 2024): array
    {
        $result = [];
        $now = intval(date('Y'));
        do {
            $result[] = $start;
            $start++;
        } while ($start <= $now);
        return $result;
    }

    public static function getMonthsId()
    {
        $result = [];
        $months = self::$idLongMonths;
        foreach ($months as $i => $month) {
            $result[] = (object)[
                'value' => $i + 1,
                'name' => $month
            ];
        }
        return $result;
    }

    public static function getPeriodsOption($year, $month)
    {
        $result = [];
        $mondays = Fun::getMondaysInMonth($year, $month);
        foreach ($mondays as $i => $monday) {
            $saturday = Fun::period($monday)[1];
            $show = self::convertPeriod("$monday - $saturday");
            $result[] = (object)[
                'value' => $monday,
                'name' => $show,
            ];
        }
        return $result;
    }

    public static function generateDateList($year, $month)
    {
        $firstDayOfMonth = date("$year-$month-01");
        $endOfMonth = Muwiza::oneMonthSince($firstDayOfMonth);
        $dates = [];

        $currentDate = $firstDayOfMonth;
        while ($currentDate <= $endOfMonth) {
            $dates[] = $currentDate;
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }

        return $dates;
    }
}
