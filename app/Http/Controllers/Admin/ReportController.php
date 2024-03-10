<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Fun;
use App\Helpers\Muwiza;
use App\Helpers\MuwizaTable;
use App\Helpers\WzExcel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
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
        $data['yearsOption'] = Fun::years(2024);
        $data['monthsOption'] = Fun::getMonthsId();
        $data['yearSelected'] = $y;
        $data['monthSelected'] = $m;
        $data['rows'] = $table->resultHTML();
        return view('admin.reports.finance', $data);
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
