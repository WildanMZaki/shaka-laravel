<?php

namespace App\Models;

use App\Helpers\Fun;
use App\Helpers\Muwiza;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklySallary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'period_start',
        'period_end',
        'presence_status',
        'total_sold',
        'min_sold',
        'uang_absen',
        'insentive',
        'main_sallary',
        'insurance',
        'kasbon',
        'unpaid_keep',
        'total_kasbon',
        'total',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function position()
    {
        return $this->belongsTo(Access::class, 'access_id');
    }

    // Ini khusus untuk generate gaji di minggu ini, untuk periode custom, maka gunakan fungsi lain saja lah ya, tapi base core perhitungannya kita gunakan saja yang sama
    public static function currentWeekFrom($user, ?string $dateTime = null, $storeImmed = true)
    {
        /**
         * 1. Cek Kehadiran
         * 2. Cek Penjualan
         * 3. Jika kehadiran perfect, hitung insentif dan uang absen
         * 4. Hitung kasbon
         */

        $currentMonday = Muwiza::firstMonday($dateTime);
        $gapok = Sallary::where('access_id', $user->access_id)->first()->nominal ?? 0;

        $workDays = Presence::workDayFrom($currentMonday);
        $presence = Presence::hadBy($user->id, $workDays);
        $period = Fun::period($currentMonday);
        $period_start = $period[0];
        $period_end = $period[1];

        $minDaily = Settings::of('Target Jual Harian SPG Freelancer');
        $workDayTotal = Settings::of('Jumlah Hari Kerja');
        $minTargetWeekly = $minDaily * $workDayTotal;

        $sallary = new WeeklySallary();
        $sallary->user_id = $user->id;
        $sallary->access_id = $user->access_id;
        $sallary->period_start = $period_start;
        $sallary->period_end = $period_end;
        $sallary->presence_status = $presence->perfect;
        $sallary->min_sold = $minTargetWeekly;
        $sallary->main_sallary = $gapok;

        if ($presence->totalHadir == 0) {
            $sallary->main_sallary = 0;
            $sallary->total = 0;
            if (!$storeImmed) {
                return $sallary;
            }
            $sallary->save();
            return;
        }

        // Cek Jabatan: 5: Team Leader, 6: SPG Freelancer, 7: SPG Training
        $salesData = Sale::fromSPG($user->id, $dateTime);
        if ($user->access_id == 5) {
            $salesData = Sale::fromLeader($user->id, $dateTime);
        }

        $sold = Sale::totalSales($salesData);
        $sallary->total_sold = $sold;

        $kasbon = Kasbon::kasbonFrom($user->id, $dateTime);
        $sallary->kasbon = $kasbon;

        // SPG Training
        if ($user->access_id == 7) {
            $dailiyInsentiveTraining = Insentif::where('access_id', $user->access_id)
                ->orderBy('sales_qty', 'asc')
                ->where('period', 'daily')
                ->get(['sales_qty', 'insentive', 'type', 'period']);

            $total = 0;
            $gajiBotolan = Settings::of('Default Gaji Botolan');
            foreach ($salesData as $sale) {
                $insentiveDaily = Insentif::getInsentive($sale->total_qty, $dailiyInsentiveTraining);
                if ($insentiveDaily == 0) {
                    $insentiveDaily = $gajiBotolan * $sale->total_qty;
                }
                $total += $insentiveDaily;
            }
            $sallary->main_sallary = $total;
            $sallary->total_kasbon = $kasbon;
            $sallary->total = $total - $kasbon;
            $nextMonday = Muwiza::nextMondayFrom(Muwiza::onlyDate($currentMonday));
            $nextWeekAvailable = Kasbon::where('type', 'kasbon')
                ->where('note', 'Gaji Minus')
                ->whereDate('created_at', $nextMonday)
                ->first();

            if ($nextWeekAvailable) {
                $nextWeekAvailable->delete();
            }

            if ($sallary->total < 0) {
                $kasbonGajiMinus = new Kasbon();
                $kasbonGajiMinus->user_id = $user->id;
                $kasbonGajiMinus->nominal = abs($sallary->total);
                $kasbonGajiMinus->status = 'approved';
                $kasbonGajiMinus->note = 'Gaji Minus';
                $kasbonGajiMinus->type = 'kasbon';
                $kasbonGajiMinus->created_at = $nextMonday . date(' H:i:s');
                $kasbonGajiMinus->save();
            }
            if (!$storeImmed) {
                return $sallary;
            }
            $sallary->save();
            return;
        }

        $insentiveMingguan = 0;
        $uangAbsen = 0;

        if ($presence->perfect) {
            $insentifData = Insentif::detailFor($user->access_id, $salesData);
            $insentiveMingguan = $insentifData->total_weekly_insentive;
            $uangAbsen = $insentifData->total_daily_insentive;
        }

        $nomMontIns = Settings::of('Nominal BPJS Bulanan');
        $totalPaidIns = self::where('user_id', $user->id)->whereBetween('period_start', [
            date('Y-m-1 00:00:00'), date('Y-m-d 23:59:59'),
        ])->sum('insurance');
        $insur = ($user->with_insurance && (int)$totalPaidIns < $nomMontIns) ? round($nomMontIns / 4, -2) : 0;
        $sallary->insurance = $insur;

        if ($user->access_id == 5) {
            // Untuk Leader, insentivenya dihitung mingguan tapi dihitung per hari gitu kan
            if ($presence->totalHadir != $workDayTotal) {
                $dailySallary = round($gapok / $workDayTotal, -3);
                $gapok = $dailySallary * $presence->totalHadir;
                $sallary->main_sallary = $gapok;
            }
            $sallary->insentive = $uangAbsen;
            $sallary->total_kasbon = $kasbon;
            $sallary->total = $gapok + $uangAbsen - $kasbon - $insur;
            if (!$storeImmed) {
                return $sallary;
            }
            $sallary->save();
            return;
        }

        $sallary->uang_absen = $uangAbsen;
        $sallary->insentive = $insentiveMingguan;

        // Khusus untuk freelance nih sekarang
        // Hitungkan Dulu Targetan, Keep, dan kasbonnya gitu
        if ($user->access_id == 6) {
            $reachTarget = $sold >= $minTargetWeekly;
            if (!$reachTarget) {
                $gajiBotolan = Settings::of('Default Gaji Botolan');
                $gapok = $sold  * $gajiBotolan;
                $sallary->main_sallary = $gapok;
            }

            // Hitung keep belum terbayar: Status: 'approved', 'pending', 
            $unpaid_keep = Kasbon::keepUnpaid($user->id, $dateTime, true);
            $sallary->unpaid_keep = $unpaid_keep;
            $total = $gapok - $kasbon;
            $total -= $unpaid_keep;
            $total -= $insur;

            $sallary->total_kasbon = $kasbon + $unpaid_keep;
            $nextMonday = Muwiza::nextMondayFrom(Muwiza::onlyDate($currentMonday));
            $nextWeekAvailable = Kasbon::where('type', 'kasbon')
                ->where('note', 'Gaji Minus')
                ->whereDate('created_at', $nextMonday)
                ->first();

            if ($nextWeekAvailable) {
                $nextWeekAvailable->delete();
            }

            if ($total < 0) {
                $kasbonFromKeep = new Kasbon();
                $kasbonFromKeep->user_id = $user->id;
                $kasbonFromKeep->nominal = abs($total);
                $kasbonFromKeep->status = 'approved';
                $kasbonFromKeep->note = 'Gaji Minus';
                $kasbonFromKeep->type = 'kasbon';
                $kasbonFromKeep->created_at = $nextMonday . date(' H:i:s');
                $kasbonFromKeep->save();
            }
            $sallary->total = $total + $uangAbsen + $insentiveMingguan;
            if (!$storeImmed) {
                return $sallary;
            }
            $sallary->save();
            return;
        }
    }
}
