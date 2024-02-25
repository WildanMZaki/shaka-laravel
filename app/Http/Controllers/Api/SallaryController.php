<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Muwiza;
use App\Http\Controllers\Controller;
use App\Models\MonthlyInsentive;
use App\Models\Presence;
use App\Models\Settings;
use App\Models\WeeklySallary;
use Cron\MonthField;
use Illuminate\Http\Request;

class SallaryController extends Controller
{
    public function index(Request $request)
    {
        $user_id = $request->attributes->get('user_id');

        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));

        $sallaries = WeeklySallary::whereYear('period_start', $year)
            ->whereMonth('period_start', $month)
            ->where('user_id', $user_id)
            ->get()
            ->map(function ($sallary) {
                $sallary->download = route('api.sallaries.download', $sallary->id);
                $sallary->period = Muwiza::convertPeriod("{$sallary->period_start} - {$sallary->period_end}");
                return $sallary;
            });

        return response()->json([
            'success' => true,
            'data' => $sallaries,
        ]);
    }

    public function detail(Request $request, $sallary_id)
    {
        $user_id = $request->attributes->get('user_id');
        try {
            $sallary = WeeklySallary::where('user_id', $user_id)
                ->where('id', $sallary_id)
                ->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return response()->json([
                'success' => false,
                'message' => 'Slip gaji tidak ditemukan',
            ]);
        }
        $sallary->download = route('api.sallaries.download', $sallary->id);
        $workDays = Presence::workDayFrom($sallary->period_start);
        $presence = Presence::hadBy($sallary->user_id, $workDays);
        $defaultTotalWorkDay = Settings::of('Jumlah Hari Kerja');
        $defaultGajiBotolan = Muwiza::ribuan(Settings::of('Default Gaji Botolan'));

        $gaji = Muwiza::ribuan($sallary->main_sallary);
        if ($sallary->user->access_id == 5 && $presence->totalHadir != 0) {
            if ($presence->totalHadir < $defaultTotalWorkDay) {
                $daily = Muwiza::ribuan($sallary->main_sallary / $presence->totalHadir);
                $total = Muwiza::ribuan($sallary->main_sallary);
                $gaji = "{$presence->totalHadir} x $daily = $total";
            }
        } else if ($sallary->user->access_id == 6) {
            if ($sallary->total_sold < $sallary->min_sold) {
                $total = Muwiza::ribuan($sallary->main_sallary);
                $gaji = "{$sallary->total_sold} x $defaultGajiBotolan = $total";
            }
        }
        $sallary->show_sallary = $gaji;

        $monthInsentive = MonthlyInsentive::where('weekly_sallaries_id', $sallary->id)->where('user_id', $sallary->user_id)->orderBy('id', 'DESC')->first();
        $sallary->bonus = is_null($monthInsentive) ? '' : $monthInsentive->insentive;
        return response()->json([
            'success' => true,
            'data' => $sallary,
        ]);
    }
}
