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
        $data['defaultSellPrice'] = Muwiza::ribuan(Settings::of('Default Harga Jual'));

        return view('admin.products.index', $data);
    }

    private function generateTable($rowsData)
    {
        return
            MuwizaTable::generate($rowsData)->extract(['merk'])
            ->col('stock', 'ribuan', '{data} Botol')
            ->col('sell_price', 'rupiah')
            ->col('sold', 'ribuan', '{data} Botol')
            ->col('status', function ($row) {
                return  $row->active ? '<span class="badge bg-label-success">Aktif</span>' : '<span class="badge bg-label-danger">Nonaktif</span>';
            })
            ->actions(['edit', 'activate', 'inactivate', ['restock' => 'info']], function ($btns, $row) {
                $btns['edit']['data']['merk'] = $row->merk;
                $btns['edit']['data']['sell_price'] = $row->sell_price;
                unset($btns[$row->active ? 'activate' : 'inactivate']);
                $used = $row->active ? 'inactivate' : 'activate';
                $btns[$used]['classIcon'] = $row->active ? 'ti ti-box-off' : 'ti ti-box';
                if ($row->active) {
                    $btns['restock']['classIcon'] = 'ti ti-shopping-cart-plus';
                    $btns['restock']['tooltip'] = 'Restok Barang';
                    $btns['restock']['url'] = route('products.restocks.form') . '?product_id=' . $row->id;
                } else {
                    unset($btns['restock']);
                }
                return $btns;
            });
    }

    public function store(Request $request)
    {
        $request->validate([
            'merk' => ['required'],
            'sell_price' => ['required'],
        ], [
            'merk.required' => 'Merk barang harus diisi',
            'sell_price.required' => 'Merk barang harus diisi',
        ]);

        $validSellPrice = Muwiza::validInt($request->sell_price);

        $inputSeo = Muwiza::seo($request->merk);
        $existingProduct = Product::where('seo', $inputSeo)->first();
        if ($existingProduct) {
            return response()->json([
                'message' => 'SEO sudah digunakan oleh produk lain',
                'errors' => [
                    'merk' => ['Produk mungkin sudah ada'],
                ],
            ], 422);
        }

        $product = new Product();
        $product->merk = $request->merk;
        $product->seo = $inputSeo;
        $product->sell_price = $validSellPrice;

        if ($product->save()) {
            return response()->json([
                'message' => 'Barang berhasil ditambahkan'
            ]);
        } else {
            return response()->json([
                'message' => 'Terjadi kesalahan'
            ], 500);
        }
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
