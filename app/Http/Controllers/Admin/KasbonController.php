<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Muwiza;
use App\Helpers\MuwizaTable;
use App\Http\Controllers\Controller;
use App\Models\Kasbon;
use App\Models\User;
use Illuminate\Http\Request;

class KasbonController extends Controller
{
    public function index(Request $request)
    {
        $start_date = $request->input('start_date', Muwiza::firstMonday());
        $end_date = $request->input('end_date', date('Y-m-d')) . '23:59:59';
        $employee_id = $request->employee_id;
        $type = $request->type_filter;

        $kasbonQuery = Kasbon::whereBetween('created_at', [$start_date, $end_date]);

        if ($employee_id) {
            $kasbonQuery->where('user_id', $employee_id);
        }
        if ($type) {
            $kasbonQuery->where('type', $type);
        }

        $kasbonData = $kasbonQuery->orderBy('created_at', 'DESC')->get();
        $table = $this->generateTable($kasbonData);
        if ($request->ajax()) {
            $rows = $table->result();
            return response()->json($rows);
        }
        $data['table'] = $table;
        $data['employees'] = User::where('access_id', '>', 4)
            ->where('active', true)
            ->orderBy('access_id', 'asc')
            ->orderBy('name', 'asc')
            ->get(['id', 'access_id', 'name']);
        $data['employeeSelected'] = $employee_id;
        $data['typeSelected'] = $type;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        return view('admin.kasbon.index', $data);
    }

    private function generateTable($rowsData): MuwizaTable
    {
        return
            MuwizaTable::generate($rowsData, function ($row, $cols) {
                $cols->employee = $row->user->name;
                $cols->keterangan = $row->note . "({$row->type})";
                return $cols;
            })->extract(['employee'])
            ->col('tanggal', ['passDate', 'created_at'])
            ->col('nominal', 'rupiah')
            ->col('status', function ($row) {
                $badges = [
                    'pending' => '<span class="badge bg-label-warning">Pending</span>',
                    'approved' => '<span class="badge bg-label-success">Approved</span>',
                    'rejected' => '<span class="badge bg-label-danger">Rejected</span>',
                    'paid' => '<span class="badge bg-label-info">Terbayar</span>',
                    'unpaid' => '<span class="badge bg-label-secondary">Dikasbon</span>',
                ];
                return $badges[$row->status];
            })
            ->col('keterangan', ['{data}', 'keterangan'])
            ->actions(['success', 'danger', 'info'], function ($btns, $row) {
                if ($row->type == 'kasbon') {
                    unset($btns['info']);
                } else {
                    $btns['info']['classIcon'] = 'ti ti-cash';
                    $btns['info']['tooltip'] = 'Terbayar';
                    $btns['info']['selector'] = 'btn-change-status';
                    $btns['info']['data']['status'] = 'paid';
                }
                $btns['success']['classIcon'] = 'ti ti-thumb-up';
                $btns['success']['tooltip'] = 'Setujui';
                $btns['success']['selector'] = 'btn-change-status';
                $btns['success']['data']['status'] = 'approved';
                $btns['danger']['classIcon'] = 'ti ti-thumb-down';
                $btns['danger']['tooltip'] = 'Tolak';
                $btns['danger']['selector'] = 'btn-change-status';
                $btns['danger']['data']['status'] = 'rejected';
                return $btns;
            });
    }

    public function manual(Request $request)
    {
        $request->validate([
            'user_id' => ['required'],
            'kasbon_date' => ['required'],
            'type' => ['required'],
            'nominal' => ['required'],
        ], [
            'user_id.required' => 'Karyawan harus dipilih',
            'kasbon_date.required' => 'Tanggal kasbon harus diisi',
            'nominal.required' => 'Nominal kasbon harus diisi',
            'type.required' => 'Tipe kasbon harus dipilih',
        ]);

        // $kasbonUserLeft = Kasbon::of($request->user_id);
        $nominal = $request->nominal;
        // if (intval($nominal) > $kasbonUserLeft) {
        //     $allowed = Muwiza::rupiah($kasbonUserLeft);
        //     return response()->json([
        //         'success' => false,
        //         'message' => "Mencapai limit, kasbon diizinkan: $allowed ",
        //     ], 404);
        // }


        $kasbon = new Kasbon();
        $kasbon->user_id = $request->user_id;
        $kasbon->nominal = $nominal;
        $kasbon->type = $request->type;
        $kasbon->note = $request->note ?? '';
        $kasbon->status = 'approved';
        $kasbon->created_at = date('Y-m-d', strtotime($request->kasbon_date)) . date(' H:i:s');
        $kasbon->save();

        return response()->json([
            'success' => true,
            'message' => 'Kasbon ditambahkan',
        ]);
    }

    public function change_status(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'status' => ['required', 'in:approved,rejected,paid'],
        ], [
            'id.required' => 'Id data kasbon diperlukan',
            'status.required' => 'Status diperlukan',
            'status.in' => 'Status harus antara approved atau rejected',
        ]);

        try {
            $kasbon = Kasbon::findOrFail($request->id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return response()->json([
                'success' => false,
                'message' => 'Data kasbon tidak ditemukan',
            ], 404);
        }

        $status = $request->status;
        $kasbon->status = $status;
        $kasbon->save();

        $msg = $status == 'approved' ? 'Pengajuan kasbon disetujui' : 'Pengajuan kasbon ditolak';

        return response()->json([
            'success' => true,
            'message' => $msg,
        ]);
    }

    public function delete(Request $request)
    {
        if (!$request->ajax()) return response()->json([
            'message' => 'Invalid request'
        ], 400);

        foreach ($request->id as $id) {
            $sale = Kasbon::find($id);
            if ($sale) {
                $sale->delete();
            } else {
                return response()->json([
                    'message' => 'Data kasbon tidak ditemukan'
                ], 400);
            }
        }

        return response()->json([
            'message' => 'Data kasbon berhasil dihapus',
        ]);
    }
}
