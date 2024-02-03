<?php

namespace App\Helpers;

use App\Models\Expenditure;
use App\Models\Presence;
use App\Models\Product;
use App\Models\Restock;
use App\Models\Sale;
use App\Models\User;

/**
 * Just a litle not, Muwiza is stand for my name: Wildan Muhammad Zaki, Mu-wi-za: hehe
 */
class Fun
{
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
            'in_formatted' => Muwiza::ribuan($coming),
            'remaining' => $remaining,
            'remaining_formatted' => Muwiza::ribuan($remaining),
            'sold' => $sold,
            'sold_formatted' => Muwiza::ribuan($sold),
        ];
    }

    public static function getProfit($period = '-14 days')
    {
        $today = date('Y-m-d 23:59:59');
        $twoWeeksAgo = date('Y-m-d 00:00:00', strtotime($period, strtotime($today)));
        $totalIncome = Sale::whereBetween('created_at', [$twoWeeksAgo, $today])->sum('total');
        $totalModal = Sale::whereBetween('created_at', [$twoWeeksAgo, $today])->sum('modal');
        return Muwiza::rupiah($totalIncome - $totalModal);
    }
    public static function getIncome($period = '-14 days')
    {
        $today = date('Y-m-d 23:59:59');
        $twoWeeksAgo = date('Y-m-d 00:00:00', strtotime($period, strtotime($today)));
        $totalIncome = Sale::whereBetween('created_at', [$twoWeeksAgo, $today])->sum('total');
        return Muwiza::rupiah($totalIncome);
    }
    public static function getExpenditures($period = '-14 days')
    {
        $today = date('Y-m-d');
        $twoWeeksAgo = date('Y-m-d', strtotime($period, strtotime($today)));
        $totalExpenditure = Expenditure::whereBetween('created_at', [$twoWeeksAgo, $today])->sum('nominal');
        return Muwiza::rupiah($totalExpenditure);
    }
}
