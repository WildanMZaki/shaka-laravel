<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Fun;
use App\Helpers\Muwiza;
use App\Helpers\MuwizaTable;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateSallaries;
use App\Models\Kasbon;
use App\Models\MonthlyInsentive;
use App\Models\Presence;
use App\Models\Sale;
use App\Models\Settings;
use App\Models\User;
use App\Models\WeeklySallary;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class SallaryController extends Controller
{
    public function index(Request $request)
    {
        $employeeId = $request->employee_id;

        $data['yearSelected'] = $request->input('year_select', date('Y'));
        $data['monthSelected'] = $request->input('month_select', date('m'));
        $data['periodSelected'] = $request->input('period_select', '');

        $sallaryQuery = WeeklySallary::whereYear('period_start', $data['yearSelected'])
            ->whereMonth('period_start', $data['monthSelected']);

        if ($data['periodSelected']) {
            $sallaryQuery->where('period_start', $data['periodSelected']);
        }

        if ($employeeId) {
            $sallaryQuery->where('user_id', $employeeId);
        }
        $totalSallary = Muwiza::rupiah($sallaryQuery->sum('total'));
        $sallaryData = $sallaryQuery->orderBy('created_at', 'DESC')->get();
        $table = $this->generateTable($sallaryData);
        if ($request->ajax()) {
            $rows = $table->result();
            $response = (object)[
                'rows' => $rows,
                'totalSallary' => $totalSallary,
            ];
            return response()->json($response);
        }
        $data['table'] = $table;
        $data['employees'] = User::where('access_id', '>=', 5)
            ->where('active', true)
            ->orderBy('access_id', 'asc')
            ->orderBy('name', 'asc')
            ->get(['id', 'access_id', 'name']);
        $data['employeeSelected'] = $employeeId;
        $period = Fun::period();
        $data['period'] = Muwiza::convertPeriod("{$period[0]} - {$period[1]}");
        $data['totalSallary'] = $totalSallary;
        $data['yearsOption'] = Fun::years(2024);
        $data['monthsOption'] = Fun::getMonthsId();
        $data['periodsOption'] = Fun::getPeriodsOption($data['yearSelected'], $data['monthSelected']);
        $data['periodsForSallary'] = Fun::getPeriodsOption(date('Y'), date('m'));
        $data['currentMonday'] = Muwiza::firstMonday();
        return view('admin.sallaries.index', $data);
    }

    private function generateTable($rowsData): MuwizaTable
    {
        // 'period', 'employee', 'sales', 'main', 'insentif', 'absen', 'total', 'kasbon',  'action',
        return
            MuwizaTable::generate($rowsData, function ($row, $cols) {
                $cols->period = "{$row->period_start} - {$row->period_end}";
                $cols->employee = $row->user->name;
                $cols->sale = $row->user->access_id == 6 ? "{$row->total_sold}/{$row->min_sold}" : $row->total_sold;
                $cols->total_insentif = $row->uang_absen + $row->insentive;
                return $cols;
            })->extract(['employee'])
            ->col('period', 'convertPeriod')
            ->col('sales', function ($row) {
                $color = 'bg-label-warning';
                if ($row->user->access_id == 6) {
                    $color = $row->total_sold >= $row->min_sold ? 'bg-label-success' : 'bg-label-danger';
                }
                return "<span class='badge $color'>{$row->sale}</span>";
            })
            ->col('main', ['ribuan', 'main_sallary'])
            ->col('total_insentif', ['ribuan', 'total_insentif'])
            ->col('insurance', 'ribuan')
            ->col('kasbon', ['ribuan', 'total_kasbon'])
            ->col('total', 'rupiah')
            ->withoutId()
            ->actions(['detail', 'success'], function ($btns, $row) {
                $btns['success']['classIcon'] = 'ti ti-download';
                $btns['success']['tooltip'] = 'Download';
                $btns['success']['selector'] = 'btn-download';
                $btns['success']['url'] = route('sallaries.download', Crypt::encryptString($row->id));
                $btns['detail']['url'] = route('sallaries.detail', Crypt::encryptString($row->id));
                return $btns;
            });
    }

    public function detail($sallary_id)
    {
        try {
            $sallaryId = Crypt::decryptString($sallary_id);
            $sallary = WeeklySallary::findOrFail($sallaryId);
            $user = $sallary->user;
        } catch (ModelNotFoundException $th) {
            abort(404);
        }

        $data['workDays'] = Presence::workDayFrom($sallary->period_start);
        $data['presences'] = Presence::hadBy($user->id, $data['workDays']);
        $data['user'] = $user;
        if ($user->access_id == 5) {
            $data['salesData'] = Sale::fromLeader($user->id, $sallary->period_start);
        } else {
            $data['salesData'] = Sale::fromSPG($user->id, $sallary->period_start);
        }
        $data['period'] = Muwiza::convertPeriod("{$sallary->period_start} - {$sallary->period_end}");
        $data['piutang'] = Kasbon::where('user_id', $user->id)
            ->whereBetween('created_at', Fun::periodWithHour($sallary->period_start))
            ->selectRaw("DATE(created_at) as date, nominal, type, status")
            ->get();

        $data['sallary_id'] = $sallary_id;
        $data['sallary'] = $sallary;

        // return response()->json($data);
        return view('admin.sallaries.detail', $data);
    }

    public function count_sallaries(Request $request)
    {
        $monday = $request->period;
        GenerateSallaries::dispatch($monday);
        return response()->json([
            'message' => 'Penghitungan Gaji Dimulai',
            'data' => [
                'monday' => $monday,
                'year' => $request->year,
                'month' => $request->month,
            ],
        ]);
    }

    public function monitor_counting(Request $request)
    {
        $monday = $request->period;
        if (!$monday) {
            $monday = Muwiza::onlyDate(Muwiza::firstMonday());
        }
        $totalUser = User::where('access_id', '>=', 5)
            ->where('active', true)
            ->whereDate('created_at', '<', $monday)
            ->count();

        $counted = WeeklySallary::where('period_start', $monday)->count();

        $progress = floor($counted * 100 / $totalUser);
        return response()->json([
            'progress' => $progress,
            'counted' => $counted,
            'total_user' => $totalUser,
        ]);
    }

    public function download($weekly_sallary_id)
    {
        try {
            $sallaryId = Crypt::decryptString($weekly_sallary_id);
            $sallary = WeeklySallary::findOrFail($sallaryId);
        } catch (ModelNotFoundException $th) {
            abort(404);
        }
        $workDays = Presence::workDayFrom($sallary->period_start);
        $presence = Presence::hadBy($sallary->user_id, $workDays);
        $defaultTotalWorkDay = Settings::of('Jumlah Hari Kerja');
        $defaultGajiBotolan = Muwiza::ribuan(Settings::of('Default Gaji Botolan'));

        $data['sallary'] = $sallary;
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
        $data['gaji'] = $gaji;

        // Cek adakah monthlyInsentive yang dimiliki
        // 1. Cek berdasarkan adakah yang punya id sama
        $monthInsentive = MonthlyInsentive::where('weekly_sallaries_id', $sallaryId)->where('user_id', $sallary->user_id)->orderBy('id', 'DESC')->first();
        $data['bonusTarget'] = is_null($monthInsentive) ? '' : $monthInsentive->insentive;
        $pdf = FacadePdf::loadView('admin.sallaries.download', $data);
        return $pdf->download("Slip Gaji {$sallary->user->name}.pdf");
        // return view('admin.sallaries.download', $data);
    }

    public function recount(Request $request)
    {
        try {
            $sallaryId = Crypt::decryptString($request->sallary_id);
            $sallary = WeeklySallary::findOrFail($sallaryId);
            $user = $sallary->user;
        } catch (ModelNotFoundException $th) {
            return response()->json([
                'message' => 'Data penggajian tidak ditemukan'
            ], 404);
        }

        $pstart = $sallary->period_start;

        $newSallary = WeeklySallary::currentWeekFrom($user, $pstart, false);

        $sallary->fill([
            'presence_status' => $newSallary->presence_status,
            'total_sold' => $newSallary->total_sold,
            'min_sold' => $newSallary->min_sold,
            'uang_absen' => $newSallary->uang_absen ?? 0,
            'insentive' => $newSallary->insentive ?? 0,
            'main_sallary' => $newSallary->main_sallary,
            'insurance' => $newSallary->insurance ?? 0,
            'kasbon' => $newSallary->kasbon ?? 0,
            'unpaid_keep' => $newSallary->unpaid_keep ?? 0,
            'total_kasbon' => $newSallary->total_kasbon ?? 0,
            'total' => $newSallary->total,
            'status' => $newSallary->status ?? 'ungiven',
        ]);

        $sallary->save();

        return response()->json([
            'message' => 'Penghitungan ulang berhasil',
            'data' => [
                'sallary' => $newSallary,
            ],
        ]);
    }
}
