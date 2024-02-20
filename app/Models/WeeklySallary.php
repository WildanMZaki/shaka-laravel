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

    // Ini khusus untuk generate gaji di minggu ini, untuk periode custom, maka gunakan fungsi lain saja lah ya, tapi base core perhitungannya kita gunakan saja yang sama
    public static function currentWeekFrom($user, ?string $dateTime = null)
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
        $sallary->period_start = $period_start;
        $sallary->period_end = $period_end;
        $sallary->presence_status = $presence->perfect;
        $sallary->min_sold = $minTargetWeekly;
        $sallary->main_sallary = $gapok;

        if ($presence->totalHadir == 0) {
            $sallary->main_sallary = 0;
            $sallary->total = 0;
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

        // SPG Training
        // Note: Perhitungan SPG Training Mirip Seperti Menghitung Insentif Harian
        if ($user->access_id == 7) {
            $total = Insentif::detailFor(7, $salesData)->total_daily_insentive;
            $sallary->total = $total;
            $sallary->save();
            return;
        }

        $kasbon = Kasbon::kasbonFrom($user->id, $dateTime);

        $insentiveMingguan = 0;
        $uangAbsen = 0;

        if ($presence->perfect) {
            $insentifData = Insentif::detailFor($user->access_id, $salesData);
            $insentiveMingguan = $insentifData->total_weekly_insentive;
            $uangAbsen = $insentifData->total_daily_insentive;
        }

        $sallary->kasbon = $kasbon;

        if ($user->access_id == 5) {
            // Untuk Leader, insentivenya dihitung mingguan tapi dihitung per hari gitu kan
            if ($presence->totalHadir != $workDayTotal) {
                $dailySallary = round($gapok / $workDayTotal, -3);
                $gapok = $dailySallary * $presence->totalHadir;
                $sallary->main_sallary = $gapok;
            }
            $sallary->insentive = $uangAbsen;
            $sallary->total_kasbon = $kasbon;
            $sallary->total = $gapok + $uangAbsen - $kasbon;
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

            $sallary->total_kasbon = $kasbon + $unpaid_keep;
            if ($total < 0) {
                $nextMonday = Muwiza::nextMondayFrom(Muwiza::onlyDate($currentMonday));
                $nextWeekAvailable = Kasbon::where('type', 'kasbon')
                    ->where('note', 'Gaji Minus')
                    ->whereDate('created_at', $nextMonday)
                    ->first();

                if ($nextWeekAvailable) {
                    $nextWeekAvailable->delete();
                }
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
            $sallary->save();
            return;
        }
    }
}
