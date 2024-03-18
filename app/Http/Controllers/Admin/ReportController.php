<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Fun;
use App\Helpers\Muwiza;
use App\Helpers\MuwizaTable;
use App\Helpers\WzExcel;
use App\Http\Controllers\Controller;
use App\Models\SalesTeam;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function sales(Request $request)
    {
        $y = $request->input('year', date('Y'));
        $m = $request->input('month', date('m'));
        $this->data['positionSelected'] = $request->input('position', '6,7');
        $month = str_pad($m, 2, '0', STR_PAD_LEFT);
        $last = date('t', strtotime("$y-$month-01"));
        $range = ["$y-$month-01", "$y-$month-$last"];

        $positions = explode(',', $this->data['positionSelected']);
        if ($this->data['positionSelected'] != '5') {
            $this->data['users'] = User::withTrashed()
                ->whereIn('access_id', $positions)
                ->whereHas('selling', function ($query) use ($range) {
                    $query->whereBetween('created_at', $range);
                })
                ->withCount(['selling as total_qty' => function ($query) use ($range) {
                    $query->select(DB::raw('sum(qty) as total_qty'))
                        ->whereBetween('created_at', $range);
                }])
                ->orderBy('total_qty', 'desc')
                ->get();
        } else {
            $this->data['users'] = User::withTrashed()
                ->where('access_id', '5')
                ->get();
        }

        $submit = $request->input('submit', 'fetch');
        $this->data['dates'] = Fun::generateDateList($y, $month);

        if ($submit == 'export') {
            return $this->salesTable($this->data['dates'], $this->data['users']);
        }

        $rowsTable = $this->salesTable($this->data['dates'], $this->data['users'], true);
        $this->data['rows'] = $rowsTable;
        // return response()->json($rowsTable);
        $this->data['yearSelected'] = $y;
        $this->data['monthSelected'] = $m;
        $this->data['target_default'] = Settings::of('Target Jual Harian SPG Freelancer');

        return view('admin.reports.sales', $this->data);
    }

    private function salesTable($dates, $employees, $needRaw = false)
    {
        $excelHeadings = ['Karyawan'];
        $superTotal = 0;
        $totEachDates = [];
        foreach ($dates as $date) {
            $excelHeadings[] = date('d', strtotime($date));
            $totEachDates[$date] = 0;
        }
        $excelHeadings[] = 'Total';

        $rows = [];
        foreach ($employees as $employee) {
            $total = 0;
            $cols = [];
            $cols['name'] = $employee->name;
            foreach ($dates as $date) {
                $qty = $employee->selling()->whereDate('created_at', $date)->sum('qty');
                $totEachDates[$date] += $qty;
                $total += intval($qty);
                $cols[date('d', strtotime($date))] = (!intval($qty)) ? '' : $qty;
            }
            $cols['total'] = $total;
            $superTotal += $total;
            $rows[] = $cols;
        }
        $lastRowCols = [];
        $lastRowCols['name'] = 'Total';
        foreach ($dates as $date) {
            $lastRowCols[date('d', strtotime($date))] = (!intval($totEachDates[$date])) ? '' : $totEachDates[$date];
        }
        $lastRowCols['total'] = $superTotal;
        $rows[] = $lastRowCols;

        if ($needRaw) {
            return $rows;
        }

        $m = date('m', strtotime($dates[0]));
        $y = date('Y', strtotime($dates[0]));
        $excel = new WzExcel('Data Laporan Penjualan', $excelHeadings, $rows);
        $monthId = Muwiza::$idLongMonths[intval($m) - 1];
        return Excel::download($excel, "Laporan Penjualan $monthId $y.xlsx");
    }

    public function teams(Request $request)
    {
        $s = $request->input('start_date', Muwiza::firstMonday());
        $e = $request->input('end_date', date('Y-m-d')) . ' 23:59:59';

        $createdTeams = SalesTeam::with('spg')
            ->whereBetween('created_at', [$s, $e])
            ->get();

        // Group the teams by date created
        $teamsGroupedByDate = $createdTeams->groupBy(function ($team) {
            return $team->created_at->format('Y-m-d');
        });

        // Group each date's teams by leader
        $result = collect();
        $teamsGroupedByDate->each(function ($teams, $date) use (&$result) {
            $result[$date] = $teams->groupBy('leader_id');
        });

        $result = $result->sortByDesc(function ($value, $key) {
            return $key;
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
