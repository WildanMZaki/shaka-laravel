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

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $data['products'] = Product::orderBy('created_at', 'desc')->get();
        $table = $this->generateTable($data['products']);
        if ($request->ajax()) {
            $rows = $table->result();
            return response()->json($rows);
        }
        $data['rows'] = $table->resultHTML();
        return view('admin.products', $data);
    }

    private function generateTable($rowsData)
    {
        return
            MuwizaTable::generate($rowsData, function ($row, $cols) {
                $cols->stock = $row->restocks->sum('qty');
                return $cols;
            })->extract(['merk'])
            ->col('stock', 'ribuan', '{data} Botol')
            ->col('sell_price', 'rupiah')
            ->col('sold', function ($row) {
                return '33 dummy';
            })
            ->col('status', function ($row) {
                return  $row->active ? '<span class="badge bg-label-success">Aktif</span>' : '<span class="badge bg-label-danger">Nonaktif</span>';
            })
            ->actions(function ($row) {
                $btns = [];
                $btns[] = MuwizaTable::$btnsDefault['edit'];
                $btns[0]['data'] = [
                    'id' => $row->id, 'merk' => $row->merk, 'sell_price' => Muwiza::ribuan($row->sell_price),
                ];
                $btns[] = MuwizaTable::$btnsDefault[$row->active ? 'inactivate' : 'activate'];
                $btns[1]['data'] = [
                    'id' => $row->id
                ];
                $btns[1]['classIcon'] = $row->active ? 'ti ti-box-off' : 'ti ti-box';
                $btns[1]['selector'] = 'btn-active-control';
                return $btns;
            });
    }

    public function restock(Request $request)
    {
        if ($request->id) {
            try {
                $restock = Restock::findOrFail($request->id);
                $data['restock'] = $restock;
                $data['merkOptions'] = [];
                $data['merkOptions'][] = (object)[
                    'id' => $restock->product->id, 'merk' => $restock->product->merk
                ];
                foreach (Product::whereNot('id', $restock->product->id)->where('active', 1)->get() as $product) {
                    $data['merkOptions'][] = (object)[
                        'id' => $product->id, 'merk' => $product->merk
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
        return view('admin.restock', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:Produk Baru,Restock'],
            'qty' => ['required', 'min:1'],
            'unit' => ['required'],
            // 'total_barang' => ['required'],
            'price_total' => ['required'],
            'price' => ['required'],
            'transaction_date' => ['required'],
            'expiration_date' => ['required'],
        ], [
            'type.required' => "Tipe belanja harus dipilih",
            'type.in' => "Tipe belanja tidak sesuai",
            'qty.required' => "Jumlah barang harus diisi",
            'unit.required' => "Satuan barang harus dipilih",
            'price_total.required' => "Harga total harus diisi",
            'transaction_date.required' => "Tanggal beli harus diisi",
            'expiration_date.required' => "Tanggal beli harus diisi",
        ]);

        $type = $request->type;
        $defaultSalePrice = Settings::of('Default Harga Jual');
        if ($type == 'Produk Baru') {
            if (!$request->merk) {
                return response()->json([
                    'message' => 'Merk Produk tidak boleh kosong',
                    'errors' => [
                        'merk' => ['Merk barang harus diisi'],
                    ],
                ], 400);
            }
            $product = new Product();
            $product->merk = $request->merk;
            $product->seo = Muwiza::seo($request->merk);
            $product->sell_price = $defaultSalePrice;
            $product->active = true;
            $product->save();

            $productId = $product->id;
            $msg = 'Produk baru berhasil ditambahkan';
        } else {
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
        $restock->type = $request->type;
        $restock->price = $request->price;
        $restock->price_total = $request->price_total;
        $restock->price_sale = $defaultSalePrice;
        $restock->save();

        return response()->json([
            'status' => 'success',
            'message' => $msg,
            'data' => [
                'product' => $product, 'restock' => $restock
            ],
        ]);
    }

    public function active_control(Request $request)
    {
        $product = Product::find($request->id);
        $product->active = !$product->active;
        $product->save();
        return response()->json([
            'status' => 'success',
            'message' => $product->active ? "Produk diaktifkan" : "Produk dinonaktifkan"
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'merk' => ['required'],
            'sell_price' => ['required'],
        ], [
            'merk.required' => 'Merk barang harus diisi',
            'sell_price.required' => 'Merk barang harus diisi',
        ]);

        $validSellPrice = Muwiza::validInt($request->sell_price);

        $product = Product::find($request->id);
        if (!$product) return response()->json([
            'message' => 'Produk tidak ditemukan'
        ], 404);

        $inputSeo = Muwiza::seo($request->merk);
        $existingProduct = Product::where('seo', $inputSeo)->where('id', '!=', $request->id)->first();
        if ($existingProduct) {
            return response()->json([
                'message' => 'SEO sudah digunakan oleh produk lain',
                'errors' => [
                    'merk' => ['Produk mungkin sudah ada'],
                ],
            ], 422);
        }

        $product->merk = $request->merk;
        $product->seo = $inputSeo;
        $product->sell_price = $validSellPrice;
        $product->save();

        return response()->json([
            'message' => 'Barang berhasil diedit'
        ]);
    }

    public function delete(Request $request)
    {
        if (!$request->ajax()) return response()->json([
            'message' => 'Invalid request'
        ], 400);

        foreach ($request->id as $id) {
            $product = Product::find($id);
            // $is_allowed = Owner::is_allowed($table->user_id, auth()->user()->id);
            // if ($is_allowed) {
            //     if ($table) $table->delete();
            // }
            if ($product) {
                $product->delete();
            } else {
                return response()->json([
                    'message' => 'Barang tidak ditemukan'
                ], 400);
            }
        }

        return response()->json([
            'message' => 'Barang berhasil dihapus',
        ]);
    }
}
