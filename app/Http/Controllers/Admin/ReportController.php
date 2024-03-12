<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Fun;
use App\Helpers\Muwiza;
use App\Helpers\MuwizaTable;
use App\Helpers\WzExcel;
use App\Http\Controllers\Controller;
use App\Models\SalesTeam;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public $data = [];
    public function __construct()
    {
        $this->data['yearsOption'] = Fun::years(2024);
        $this->data['monthsOption'] = Fun::getMonthsId();
    }

    public function presences(Request $request)
    {
        $y = $request->input('year', date('Y'));
        $m = $request->input('month', date('m'));
        $this->data['yearSelected'] = $y;
        $this->data['monthSelected'] = $m;
        $month = str_pad($m, 2, '0', STR_PAD_LEFT);
        $last = date('t', strtotime("$y-$month-01"));
        // return response()->json($lastDate);

        $this->data['users'] = User::withTrashed()
            ->whereHas('presences', function ($query) use ($y, $month, $last) {
                $query->whereBetween('date', ["$y-$month-01", "$y-$month-$last"]);
            })
            ->where('access_id', '>=', 2)
            ->where('active', true)
            ->get();

        $submit = $request->input('submit', 'fetch');
        $this->data['dates'] = Fun::generateDateList($y, $month);

        if ($submit == 'export') {
            return $this->presencesTable($this->data['dates'], $this->data['users']);
        }

        return view('admin.reports.presences', $this->data);
    }

    private function presencesTable($dates, $employees)
    {
        $excelHeadings = ['Karyawan'];
        foreach ($dates as $date) {
            $excelHeadings[] = date('d', strtotime($date));
        }
        $rows = [];
        foreach ($employees as $employee) {
            $cols = [];
            $cols['name'] = $employee->name;
            foreach ($dates as $date) {
                $presence = $employee->presences->where('date', $date)->first();
                $presenceSymbol = '';
                $suffix = '';
                if ($presence) {
                    switch ($presence->flag) {
                        case 'hadir':
                            $presenceSymbol = '*';
                            break;
                        case 'sakit':
                            $presenceSymbol = 's';
                            break;
                        case 'izin':
                            $presenceSymbol = 'i';
                            break;
                    }

                    switch ($presence->status) {
                        case 'approved':
                            $suffix = '';
                            break;
                        case 'pending':
                            $suffix = ' (p)';
                            break;
                        case 'rejected':
                            $suffix = ' (r)';
                            break;
                    }
                }
                $cols[date('d', strtotime($date))] = $presenceSymbol . $suffix;
            }
            $rows[] = $cols;
        }
        $m = date('m', strtotime($dates[0]));
        $y = date('Y', strtotime($dates[0]));
        $excel = new WzExcel('Data Laporan Absensi', $excelHeadings, $rows);
        $monthId = Muwiza::$idLongMonths[intval($m) - 1];
        return Excel::download($excel, "Laporan Absensi $monthId $y.xlsx");
    }

    public function teams(Request $request) {
        $s = $request->input('start_date', Muwiza::firstMonday());
        $e = $request->input('end_date', date('Y-m-d')) . ' 23:59:59';

        $createdTeams = SalesTeam::with('spg')
            ->whereBetween('created_at', [$s, $e])
            ->get();

        // Group the teams by date created
        $teamsGroupedByDate = $createdTeams->groupBy(function($team) {
            return $team->created_at->format('Y-m-d');
        });

        // Group each date's teams by leader
        $result = collect();
        $teamsGroupedByDate->each(function($teams, $date) use (&$result) {
            $result[$date] = $teams->groupBy('leader_id');
        });

        $data['start_date'] = $s;
        $data['end_date'] = $e;
        $data['result'] = $result;

        return view('admin.reports.teams', $data);
    }

    public function finance(Request $request)
    {
        $y = $request->input('year', date('Y'));
        $m = $request->input('month', date('m'));
        $submit = $request->input('submit', 'fetch');

        $mondays = Fun::getMondaysInMonth($y, $m);
        $periods = [];
        foreach ($mondays as $monday) {
            [$s, $e] = Fun::period($monday);
            $periods[] = (object)[
                'start' => "$s 00:00:00",
                'end' => "$e 23:59:59",
                'period' => "$s - $e",
                'between' => Fun::periodWithHour($monday),
            ];
        }
        $table = $this->generateFinanceTable($periods);
        if ($submit == 'export') {
            $excelHeadings = [
                'Periode', 'Pendapatan', 'Pengeluaran', 'Penggajian', 'Laba'
            ];
            $excel = new WzExcel('Data Laporan Keuangan', $excelHeadings, $table->result());
            $monthId = Muwiza::$idLongMonths[intval($m) - 1];
            return Excel::download($excel, "Laporan Keuangan $monthId $y.xlsx");
        }
        if ($request->ajax()) {
            return response()->json($table->result());
        }
        $this->data['yearSelected'] = $y;
        $this->data['monthSelected'] = $m;
        $this->data['rows'] = $table->resultHTML();
        return view('admin.reports.finance', $this->data);
    }

    private function generateFinanceTable($periods): MuwizaTable
    {
        return MuwizaTable::generate($periods, function ($row, $cols) {
            $cols->sallaries = Fun::getGivenSallaries($row->between);
            return $cols;
        })
            ->col('period', 'convertPeriod')
            ->col('income', function ($row) {
                return Fun::getIncome($row->between);
            })
            ->col('expenditure', function ($row) {
                return Fun::getExpenditures($row->between);
            })
            ->col('sallaries', 'rupiah')
            ->col('profit', function ($row) {
                return Fun::getProfit($row->between);
            });
    }
}
