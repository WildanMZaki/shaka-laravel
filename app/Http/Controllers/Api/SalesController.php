<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Settings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->attributes->get('user');
        $status = $request->status;
        if (!$status) {
            $status = 'done';
        }

        $mySelling = $user->selling()
            ->with(['product' => function ($query) {
                $query->select('id', 'merk');
            }])
            ->where('status', $status)
            ->whereDate('created_at', now())
            ->get(['id', 'qty', 'created_at', 'total', 'status', 'product_id']);

        return response()->json([
            'success' => true, 'data' => $mySelling,
        ]);
    }

    public function statistics(Request $request)
    {
        $user = $request->attributes->get('user');

        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfDay = Carbon::now()->endOfDay();

        $dailyTarget = Settings::of('Target Jual Harian SPG Freelancer');
        $totalToday = $user->selling()
            ->where('status', 'done')
            ->whereDate('created_at', $today)
            ->count();

        $totalInWeek = $user->selling()
            ->where('status', 'done')
            ->whereBetween('created_at', [$startOfWeek, $endOfDay])
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'dailyTarget' => $dailyTarget,
                'totalToday' => $totalToday,
                'totalInWeek' => $totalInWeek,
            ],
        ]);
    }

    public function products()
    {
        $products = Product::withPositiveStock()->where('active', true)->get(['id', 'merk', 'sell_price']);
        return response()->json([
            'success' => true, 'data' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qty' => 'required',
            'product_id' => 'required',
        ], [
            'qty.required' => 'Qty diperlukan',
            'product_id.required' => 'Id product diperlukan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user_id = $request->attributes->get('user_id');
        $product_id = $request->product_id;
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
        $sale->save();

        return response()->json([
            'success' => true,
            'message' => 'Barang disimpan',
        ]);
    }

    public function save(Request $request)
    {
        $user_id = $request->attributes->get('user_id');

        $affectedRows = Sale::where('user_id', $user_id)
            ->whereDate('created_at', now())
            ->where('status', 'processed')
            ->update(['status' => 'done']);

        return response()->json([
            'success' => true,
            'message' => 'Penjualan berhasil ditambahkan'
        ]);
    }

    public function update(Request $request, $sales_id)
    {
        $validator = Validator::make($request->all(), [
            'qty' => 'required',
            'product_id' => 'required',
        ], [
            'qty.required' => 'Qty diperlukan',
            'product_id.required' => 'Id product diperlukan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user_id = $request->attributes->get('user_id');
        $product_id = $request->product_id;
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

        $sale = Sale::find($sales_id);
        $sale->product_id = $product_id;
        $sale->user_id = $user_id;
        $sale->qty = $qty;
        $sale->modal_item = $last_modal;
        $sale->modal = $total_modal;
        $sale->price_item = $price_item;
        $sale->total = $total;
        $sale->save();

        return response()->json([
            'success' => true,
            'message' => 'Penjualan diupdate',
        ]);
    }

    public function delete(Request $request, $sales_id)
    {
        $user_id = $request->attributes->get('user_id');

        $sale = Sale::where('user_id', $user_id)->where('id', $sales_id)->first();
        if ($sale) {
            $sale->delete();
            return response()->json([
                'success' => true,
                'message' => 'Penjualan berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Penjualan tidak ditemukan'
            ], 404);
        }
    }
}
