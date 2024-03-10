<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Muwiza;
use App\Helpers\MuwizaTable;
use App\Http\Controllers\Controller;
use App\Models\Expenditure;
use App\Models\User;
use Illuminate\Http\Request;

class ExpenditureController extends Controller
{
    public function index(Request $request)
    {
        $start_date = $request->input('start_date', Muwiza::firstMonday());
        $end_date = $request->input('end_date', date('Y-m-d')) . ' 23:59:59';

        $leader_id = $request->leader_id;

        $expenditureQuery = Expenditure::whereBetween('created_at', [$start_date, $end_date]);

        if ($leader_id) {
            $expenditureQuery->where('user_id', $leader_id);
        }
        $totalExpenditure = Muwiza::rupiah($expenditureQuery->sum('nominal'));
        $expenditureData = $expenditureQuery->orderBy('created_at', 'DESC')->get();
        $table = $this->generateTable($expenditureData);
        if ($request->ajax()) {
            $rows = $table->result();
            $response = (object)[
                'rows' => $rows,
                'totalExpenditure' => $totalExpenditure,
            ];
            return response()->json($response);
        }
        $data['table'] = $table;
        $data['leaders'] = User::where('access_id', '>', 1)
            ->where('access_id', '<=', 5)
            ->where('active', true)
            ->orderBy('access_id', 'asc')
            ->orderBy('name', 'asc')
            ->get(['id', 'access_id', 'name']);
        $data['leaderSelected'] = $leader_id;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['totalExpenditure'] = $totalExpenditure;
        return view('admin.expenditures.index', $data);
    }

    private function generateTable($rowsData): MuwizaTable
    {
        return
            MuwizaTable::generate($rowsData, function ($row, $cols) {
                $cols->leader_name = $row->user->name;
                return $cols;
            })->extract(['leader_name'])
            ->col('tanggal', ['passDate', 'created_at'])
            ->col('nominal', 'rupiah')
            ->col('keterangan', ['{data}', 'note']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => ['required'],
            'expenditures_date' => ['required'],
            'nominal' => ['required'],
            'note' => ['required'],
        ], [
            'user_id.required' => 'Leader harus dipilih',
            'expenditures_date.required' => 'Tanggal pengeluaran harus diisi',
            'nominal.required' => 'Nominal pengeluaran harus diisi',
            'note.required' => 'Keterangan pengeluaran harus diisi',
        ]);

        $expenditure = new Expenditure();
        $expenditure->user_id = $request->user_id;
        $expenditure->nominal = $request->nominal;
        $expenditure->note = $request->note;
        $expenditure->created_at = date('Y-m-d', strtotime($request->expenditures_date)) . date(' H:i:s');
        $expenditure->save();

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran ditambahkan',
        ]);
    }

    public function delete(Request $request)
    {
        if (!$request->ajax()) return response()->json([
            'message' => 'Invalid request'
        ], 400);

        foreach ($request->id as $id) {
            $sale = Expenditure::find($id);
            if ($sale) {
                $sale->delete();
            } else {
                return response()->json([
                    'message' => 'Data pengeluaran tidak ditemukan'
                ], 400);
            }
        }

        return response()->json([
            'message' => 'Pengeluaran berhasil dihapus',
        ]);
    }
}
