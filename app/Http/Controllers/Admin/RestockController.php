<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Muwiza;
use App\Helpers\MuwizaTable;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Restock;
use App\Models\Settings;
use App\Models\Unit;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class RestockController extends Controller
{
    public function index(Request $request)
    {
        $data['restocks'] = Restock::orderBy('created_at', 'desc')->get();
        $table = $this->generateTable($data['restocks']);
        if ($request->ajax()) {
            $rows = $table->result();
            return response()->json($rows);
        }
        $data['table'] = $table;
        return view('admin.products.restocks', $data);
    }

    private function generateTable($rowsData): MuwizaTable
    {
        return MuwizaTable::generate($rowsData, function ($row, $cols) {
            $cols->merk = $row->product->deleted_at ? "<span class='text-danger'>{$row->product->merk}</span>" : $row->product->merk;
            return $cols;
        })->extract(['merk'])
            ->col('restock_date', 'simpleDate')
            ->col('qty', 'ribuan', '{data} Botol')
            ->col('modal', ['rupiah', 'price_total'])
            ->col('expDate', ['simpleDate', 'expiration_date'])
            ->actions(['detail', 'edit'], function ($btns, $row) {
                $btns['edit']['url'] = route('products.restocks.edit', $row->id);
                $btns['detail']['classIcon'] = 'ti ti-notes';
                return $btns;
            });
    }

    public function detail($id)
    {
        try {
            $restock = Restock::findOrFail($id);
        } catch (ModelNotFoundException $th) {
            abort(404);
        }
        return response()->json([
            'merk' => $restock->product->merk,
            'qty' => Muwiza::ribuan($restock->qty) . ' Botol',
            'modal' => Muwiza::rupiah($restock->price_total),
            'restock_date' => Muwiza::simpleDate($restock->restock_date),
            'expiration_date' => Muwiza::simpleDate($restock->expiration_date),
            'description' => $restock->description,
        ]);
    }

    public function restock(Request $request)
    {
        if ($request->product_id) {
            try {
                $product = Product::where('active', 1)->findOrFail($request->product_id);
                $data['product'] = $product;
                $data['merkOptions'] = [];
                $data['merkOptions'][] = (object)[
                    'id' => $product->id, 'merk' => $product->merk
                ];
                foreach (Product::whereNot('id', $product->id)->where('active', 1)->get() as $product_item) {
                    $data['merkOptions'][] = (object)[
                        'id' => $product_item->id, 'merk' => $product_item->merk
                    ];
                }
                $data['units'] = Unit::orderBy('id', 'desc')->get();
            } catch (ModelNotFoundException $th) {
                abort(404);
            }
        } else {
            $data['merkOptions'] = Product::select('id', 'merk')->where('active', 1)->get();
            $data['units'] = Unit::orderBy('id', 'desc')->get();
        }
        return view('admin.products.restock', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'qty' => ['required', 'min:1'],
            'unit' => ['required'],
            'merk_id' => ['required'],
            'price_total' => ['required'],
            'price' => ['required'],
            'transaction_date' => ['required'],
            'expiration_date' => ['required'],
        ], [
            'qty.required' => "Jumlah barang harus diisi",
            'merk_id.required' => "Merk barang harus dipilih",
            'unit.required' => "Satuan barang harus dipilih",
            'price_total.required' => "Harga total harus diisi",
            'transaction_date.required' => "Tanggal beli harus diisi",
            'expiration_date.required' => "Tanggal beli harus diisi",
        ]);

        // Validasi ada atau tidaknya produk
        try {
            $product = Product::findOrFail($request->merk_id);
            $productId = $product->id;
            $msg = 'Restock barang berhasil';
        } catch (ModelNotFoundException $th) {
            return response()->json([
                'message' => 'Produk tidak ditemukan',
                'errors' => [
                    'merk_id' => ['Merk barang harus dipilih'],
                ],
            ], 404);
        }

        if (!$request->price_total) {
            return response()->json([
                'message' => 'Harga barang tidak boleh kosong',
                'errors' => [
                    'price_total' => ['Harga barang harus diisi'],
                ],
            ], 400);
        }

        $restockDate = strtotime($request->transaction_date);
        $expDate = strtotime($request->expiration_date);
        if ($expDate <= time()) {
            return response()->json([
                'message' => 'Tanggal kadaluarsa tidak boleh kurang dari hari ini',
                'errors' => [
                    'expiration_date' => ['Tanggal kadaluarsa tidak boleh kurang dari hari ini'],
                ],
            ], 400);
        }
        if ($expDate <= $restockDate) {
            return response()->json([
                'message' => 'Tanggal kadaluarsa tidak boleh kurang dari tanggal transaksi',
                'errors' => [
                    'expiration_date' => ['Tanggal kadaluarsa tidak boleh kurang dari tanggal transaksi'],
                ],
            ], 400);
        }
        // Next bisa juga tambah rule, kapan / berapa jangka minimal tanggal kadaluarsa
        $restock = new Restock();
        $restock->product_id = $productId;
        $restock->qty = $request->qty * $request->unit;
        $restock->restock_date = date('Y-m-d H:i:s', $restockDate);
        $restock->expiration_date = date('Y-m-d H:i:s', $expDate);
        $restock->price = $request->price;
        $restock->price_total = $request->price_total;
        $restock->price_sale = $product->sell_price;
        $restock->description = $request->description;
        $restock->save();

        return response()->json([
            'status' => 'success',
            'message' => $msg,
            'data' => [
                'product' => $product, 'restock' => $restock
            ],
        ]);
    }

    public function edit(Request $request, $id)
    {
        try {
            $restock = Restock::findOrFail($id);
            $restock->formatted_modal = Muwiza::ribuan($restock->price_total);
            $price_item = Muwiza::ceilToHundreds($restock->price_total / $restock->qty);
            $restock->formatted_price_item = Muwiza::rupiah($price_item);
        } catch (ModelNotFoundException $th) {
            abort(404);
        }

        $data['merkOptions'][] = (object)[
            'id' => $restock->product->id, 'merk' => $restock->product->merk
        ];
        foreach (Product::whereNot('id', $restock->product->id)->where('active', 1)->get() as $product) {
            $data['merkOptions'][] = (object)[
                'id' => $product->id, 'merk' => $product->merk
            ];
        }
        $unitsAvailable = Unit::orderBy('qty', 'desc')->get();
        $skipedUnits = [];
        $data['units'] = [];
        $totalInputted = $restock->qty;
        foreach ($unitsAvailable as $un) {
            if ($restock->qty % $un->qty == 0 && count($data['units']) == 0) {
                $totalInputted = $restock->qty / $un->qty;
                $data['units'][] = $un;
            } else {
                $skipedUnits[] = $un;
            }
        }
        $restock->jumlah = $totalInputted;
        $data['units'] = array_merge($data['units'], $skipedUnits);
        $data['restock'] = $restock;
        return view('admin.products.edit-restock', $data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'qty' => ['required', 'min:1'],
            'unit' => ['required'],
            'merk_id' => ['required'],
            'price_total' => ['required'],
            'price' => ['required'],
            'transaction_date' => ['required'],
            'expiration_date' => ['required'],
        ], [
            'id.required' => "Restock id harus ada",
            'qty.required' => "Jumlah barang harus diisi",
            'merk_id.required' => "Merk barang harus dipilih",
            'unit.required' => "Satuan barang harus dipilih",
            'price_total.required' => "Harga total harus diisi",
            'transaction_date.required' => "Tanggal beli harus diisi",
            'expiration_date.required' => "Tanggal beli harus diisi",
        ]);

        // Validasi ada atau tidaknya restock & produk
        try {
            $restock = Restock::findOrFail($request->id);
            $product = Product::findOrFail($request->merk_id);
            $productId = $product->id;
            $msg = 'Edit data restock barang berhasil';
        } catch (ModelNotFoundException $th) {
            return response()->json([
                'message' => 'Produk atau data restock tidak ditemukan',
                'errors' => [
                    'merk_id' => ['Merk barang harus dipilih'],
                ],
            ], 404);
        }

        if (!$request->price_total) {
            return response()->json([
                'message' => 'Harga barang tidak boleh kosong',
                'errors' => [
                    'price_total' => ['Harga barang harus diisi'],
                ],
            ], 400);
        }

        $restockDate = strtotime($request->transaction_date);
        $expDate = strtotime($request->expiration_date);
        if ($expDate <= time()) {
            return response()->json([
                'message' => 'Tanggal kadaluarsa tidak boleh kurang dari hari ini',
                'errors' => [
                    'expiration_date' => ['Tanggal kadaluarsa tidak boleh kurang dari hari ini'],
                ],
            ], 400);
        }
        if ($expDate <= $restockDate) {
            return response()->json([
                'message' => 'Tanggal kadaluarsa tidak boleh kurang dari tanggal transaksi',
                'errors' => [
                    'expiration_date' => ['Tanggal kadaluarsa tidak boleh kurang dari tanggal transaksi'],
                ],
            ], 400);
        }
        // Next bisa juga tambah rule, kapan / berapa jangka minimal tanggal kadaluarsa
        $restock->product_id = $productId;
        $restock->qty = $request->qty * $request->unit;
        $restock->restock_date = date('Y-m-d H:i:s', $restockDate);
        $restock->expiration_date = date('Y-m-d H:i:s', $expDate);
        $restock->price = $request->price;
        $restock->price_total = $request->price_total;
        $restock->price_sale = $product->sell_price;
        $restock->description = $request->description;
        $restock->save();

        return response()->json([
            'status' => 'success',
            'message' => $msg,
            'data' => [
                'product' => $product, 'restock' => $restock
            ],
        ]);
    }

    public function delete(Request $request)
    {
        if (!$request->ajax()) return response()->json([
            'message' => 'Invalid request'
        ], 400);

        foreach ($request->id as $id) {
            $restockData = Restock::find($id);
            if ($restockData) {
                $restockData->delete();
            } else {
                return response()->json([
                    'message' => 'Riwayat belanja tidak ditemukan'
                ], 400);
            }
        }

        return response()->json([
            'message' => 'Riwayat belanja berhasil dihapus',
        ]);
    }
}
