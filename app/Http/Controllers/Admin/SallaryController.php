<?php

namespace App\Http\Controllers\Admin;

use PDF;
use App\Helpers\Fun;
use App\Helpers\Muwiza;
use App\Helpers\MuwizaTable;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateSallaries;
use App\Models\Presence;
use App\Models\Settings;
use App\Models\User;
use App\Models\WeeklySallary;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SallaryController extends Controller
{
    public function rules(Request $request)
    {
    }
    public function index(Request $request)
    {
        $start_date = $request->input('start_date', Muwiza::firstMonday());
        $employeeId = $request->employee_id;
        $sallaryQuery = WeeklySallary::whereYear('period_start', date('Y'))
            ->whereMonth('period_start', date('m'));

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
        $data['period_start'] = $start_date;
        $data['totalSallary'] = $totalSallary;
        $data['yearsOption'] = Fun::years(2024);
        $data['monthsOption'] = Fun::getMonthsId();
        $data['periodsOption'] = Fun::getPeriodsOption(date('Y'), date('m'));
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
            ->col('kasbon', ['ribuan', 'total_kasbon'])
            ->col('total', 'rupiah')
            ->withoutId()
            ->actions(['detail', 'success'], function ($btns, $row) {
                $btns['success']['classIcon'] = 'ti ti-download';
                $btns['success']['tooltip'] = 'Download';
                $btns['success']['selector'] = 'btn-download';
                $btns['success']['url'] = route('sallaries.download', $row->id);
                return $btns;
            });
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
        $totalUser = User::where('access_id', '>=', 5)->where('active', true)->count();
        $monday = $request->period;
        if (!$monday) {
            $monday = Muwiza::onlyDate(Muwiza::firstMonday());
        }
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
            $sallary = WeeklySallary::findOrFail($weekly_sallary_id);
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

        $pdf = FacadePdf::loadView('admin.sallaries.download', $data);
        return $pdf->download("Slip Gaji {$sallary->user->name}");
        // return view('admin.sallaries.download', $data);
    }
}
