<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\MuwizaTable;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $today = date('Y-m-d');
        $start_date = $request->input('start_date', date('Y-m-d', strtotime('last Monday', strtotime($today)))) . ' 00:00:00';
        $end_date = $request->input('end_date', $today) . ' 23:59:59';
        $product_id = $request->product_id;
        $spg_id = $request->spg_id;

        $salesQuery = Sale::whereBetween('created_at', [$start_date, $end_date]);
        if ($product_id) {
            $salesQuery->where('product_id', $product_id);
        }
        if ($spg_id) {
            $salesQuery->where('user_id', $spg_id);
        }
        $salesData = $salesQuery->get();
        $table = $this->generateTable($salesData);
        if ($request->ajax()) {
            $rows = $table->result();
            return response()->json($rows);
        }
        $data['table'] = $table;
        $data['activeProducts'] = Product::withPositiveStock()->where('active', true)->get(['id', 'merk']);
        $data['spgs'] = User::whereIn('access_id', [6, 7])->where('active', true)->get(['id', 'access_id', 'name']);
        return view('admin.sales.index', $data);
    }

    private function generateTable($rowsData): MuwizaTable
    {
        return
            MuwizaTable::generate($rowsData, function ($row, $cols) {
                $cols->spg_name = $row->user->name;
                $cols->merk = $row->product->merk;
                return $cols;
            })->extract(['spg_name', 'merk'])
            ->col('tanggal', ['passDate', 'created_at'])
            ->col('qty', '{data} Botol')
            ->col('pendapatan', ['rupiah', 'total']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required'],
            'user_id' => ['required'],
            'sales_date' => ['required'],
            'qty' => ['required'],
        ], [
            'product_id.required' => 'Nama karyawan harus diisi',
            'user_id.required' => 'Nomor ponsel harus diisi',
            'sales_date.required' => 'Email harus diisi',
            'qty.required' => 'NIK harus diisi',
        ]);

        $user_id = $request->user_id;
        $product_id = $request->product_id;
        $sales_date = $request->sales_date;
        $qty = $request->qty;

        $product = Product::find($product_id);

        if ($product->stock < $qty) {
            return response()->json([
                'success' => false,
                'message' => 'Stok kurang',
            ], 400);
        }

        $price_item = $product->sell_price;
        $total = $price_item * $qty;

        // Untuk saat ini, penghitungan modal dilakukan hanya melihat restock terakhir
        $last_modal = $product->restocks()->latest()->first()->price;
        $total_modal = ($last_modal ?? 0) * $qty;

        $sale = new Sale();
        $sale->product_id = $product_id;
        $sale->user_id = $user_id;
        $sale->qty = $qty;
        $sale->modal_item = $last_modal;
        $sale->modal = $total_modal;
        $sale->price_item = $price_item;
        $sale->total = $total;
        $sale->status = 'done';
        $sale->created_at = date('Y-m-d', strtotime($sales_date)) . date(' H:i:s');
        $sale->save();

        $activeProducts = Product::withPositiveStock()->where('active', true)->get(['id', 'merk']);
        return response()->json([
            'success' => true,
            'message' => 'Penjualan disimpan',
            'data' => [
                'activeProducts' => $activeProducts,
            ],
        ]);
    }

    public function delete(Request $request)
    {
        if (!$request->ajax()) return response()->json([
            'message' => 'Invalid request'
        ], 400);

        foreach ($request->id as $id) {
            $sale = Sale::find($id);
            if ($sale) {
                $sale->delete();
            } else {
                return response()->json([
                    'message' => 'Data penjualan tidak ditemukan'
                ], 400);
            }
        }

        $activeProducts = Product::withPositiveStock()->where('active', true)->get(['id', 'merk']);
        return response()->json([
            'message' => 'Karyawan berhasil dihapus',
            'data' => [
                'activeProducts' => $activeProducts,
            ],
        ]);
    }
}
