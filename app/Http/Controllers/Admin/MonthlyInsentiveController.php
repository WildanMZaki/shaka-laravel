<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Fun;
use App\Helpers\Muwiza;
use App\Helpers\MuwizaTable;
use App\Http\Controllers\Controller;
use App\Models\Insentif;
use App\Models\MonthlyInsentive;
use App\Models\User;
use App\Models\WeeklySallary;
use Illuminate\Http\Request;

class MonthlyInsentiveController extends Controller
{
    public function index(Request $request)
    {
        $employeeId = $request->employee_id;

        $data['yearSelected'] = $request->input('year_select', date('Y'));
        $data['monthSelected'] = $request->input('month_select', date('m'));

        $latestCreated = MonthlyInsentive::orderBy('created_at', 'DESC')->first();

        $data['latestInsentive'] = is_null($latestCreated) ? date('Y-m-d', strtotime(date('Y-m-d') . ' -45 day')) : Muwiza::onlyDate($latestCreated->created_at);
        $data['recomendedDate'] = date('Y-m-1');

        $monthlyInsentiveQuery = MonthlyInsentive::whereYear('created_at', $data['yearSelected'])
            ->whereMonth('created_at', $data['monthSelected']);

        if ($employeeId) {
            $monthlyInsentiveQuery->where('user_id', $employeeId);
        }
        $monthlyInsentivesData = $monthlyInsentiveQuery->orderBy('created_at', 'DESC')->get();
        $table = $this->generateTable($monthlyInsentivesData);
        if ($request->ajax()) {
            $rows = $table->result();
            return response()->json($rows);
        }
        $data['table'] = $table;
        $data['employees'] = User::where('access_id', '>=', 5)
            ->where('active', true)
            ->whereHas('monthlyIncentives')
            ->orderBy('access_id', 'asc')
            ->orderBy('name', 'asc')
            ->get(['id', 'access_id', 'name']);
        $data['yearsOption'] = Fun::years(2024);
        $data['monthsOption'] = Fun::getMonthsId();
        $data['employeeSelected'] = $employeeId;
        return view('admin.sallaries.monthly', $data);
    }

    private function generateTable($rowsData): MuwizaTable
    {
        // 'period', 'employee', 'sales', 'insentive',
        return
            MuwizaTable::generate($rowsData, function ($row, $cols) {
                $cols->period = "{$row->start_date} - {$row->end_date}";
                $cols->employee = $row->user->name;
                $cols->sales = $row->sales_qty;
                return $cols;
            })->extract(['employee', 'sales', 'insentive'])
            ->col('period', 'convertPeriod')
            ->col('counted_at', ['simpleDate', 'created_at']);
    }

    public function count(Request $request)
    {
        $request->validate([
            'start_date' => ['required'],
        ], [
            'start_date.required' => 'Tanggal mulai harus ditentukan',
        ]);

        $start = date('Y-m-d', strtotime($request->start_date));

        $oneMonthDate = Muwiza::oneMonthSince($start);

        $userWithSelling = User::where('access_id', 6)
            ->where('active', true)
            ->with(['selling' => function ($query) use ($start, $oneMonthDate) {
                $query->whereBetween('created_at', [$start, $oneMonthDate]);
            }])
            ->get()
            ->map(function ($user) {
                $totalQty = $user->selling->sum('qty');
                return (object)[
                    'id' => $user->id,
                    'access_id' => $user->access_id,
                    'name' => $user->name,
                    'total_qty' => $totalQty,
                ];
            });

        $monthlyInsentifs = Insentif::where('period', 'monthly')->get()->toArray();

        $monthlyInsentiveGiven = [];
        foreach ($userWithSelling as $userSale) {
            $accessId = $userSale->access_id;
            $insentifs = array_filter($monthlyInsentifs, function ($element) use ($accessId) {
                return $element['access_id'] == $accessId;
            });

            $insentifs = array_values($insentifs);

            $insentive = Insentif::getInsentive($userSale->total_qty, $insentifs);
            if ($insentive != 0) {
                $weeklyCreated = WeeklySallary::whereDate('created_at', date('Y-m-d'))->where('user_id', $userSale->id)->first();
                if (!$weeklyCreated) {
                    $weeklyCreated = WeeklySallary::where('user_id', $userSale->id)->orderBy('created_at', 'desc')->first();
                }
                $monthlyInsentiveGiven[] = [
                    'user_id' => $userSale->id,
                    'weekly_sallaries_id' => is_null($weeklyCreated) ? '' : $weeklyCreated->id,
                    'start_date' => $start,
                    'end_date' => $oneMonthDate,
                    'sales_qty' => $userSale->total_qty,
                    'insentive' => $insentive,
                    'type' => is_int($insentive) ? 'money' : 'thing',
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }
        }

        if (count($monthlyInsentiveGiven)) {
            MonthlyInsentive::insert($monthlyInsentiveGiven);
        }

        return response()->json([
            'message' => 'Perhitungan selesai',
        ]);
    }
}
