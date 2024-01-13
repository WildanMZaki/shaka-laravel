<?php

namespace App\Http\Controllers;

use App\Helpers\MuwizaTable;
use App\Models\Access;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $data['employees'] = User::whereIn('access_id', [3, 4])->orderBy('created_at', 'desc')->get();
        $table = $this->generateTable($data['employees']);
        if ($request->ajax()) {
            $rows = $table->result();
            return response()->json($rows);
        }
        $data['rows'] = $table->resultHTML();
        $data['positions'] = Access::whereIn('id', [3, 4])->get();
        $data['team_leaders'] = User::where('id', 3)->get();
        return view('admin.employees.index', $data);
    }

    private function generateTable($rowsData)
    {
        return
            MuwizaTable::generate($rowsData, function ($row, $cols) {
                $cols->position = $row->access->name;
                return $cols;
            })->extract(['name', 'phone', 'email', 'position'])
            ->col('status', function ($row) {
                return  $row->active ? '<span class="badge bg-label-success">Aktif</span>' : '<span class="badge bg-label-danger">Nonaktif</span>';
            })
            ->actions(['detail', 'edit', 'activate', 'inactivate'], function ($btns, $row) {
                // $btns['edit']['data']['merk'] = $row->merk;
                // $btns['edit']['data']['sell_price'] = $row->sell_price;
                unset($btns[$row->active ? 'activate' : 'inactivate']);
                $used = $row->active ? 'inactivate' : 'activate';
                $btns[$used]['classIcon'] = $row->active ? 'ti ti-user-off' : 'ti ti-user-check';
                return $btns;
            });
    }

    public function active_control(Request $request)
    {
        $employee = User::find($request->id);
        $employee->active = !$employee->active;
        $employee->save();
        return response()->json([
            'status' => 'success',
            'message' => $employee->active ? "Karyawan diaktifkan" : "Karyawan dinonaktifkan"
        ]);
    }

    // public function update(Request $request)
    // {
    //     $request->validate([
    //         'merk' => ['required'],
    //         'sell_price' => ['required'],
    //     ], [
    //         'merk.required' => 'Merk barang harus diisi',
    //         'sell_price.required' => 'Merk barang harus diisi',
    //     ]);

    //     $validSellPrice = Muwiza::validInt($request->sell_price);

    //     $product = Product::find($request->id);
    //     if (!$product) return response()->json([
    //         'message' => 'Produk tidak ditemukan'
    //     ], 404);

    //     $inputSeo = M::seo($request->merk);
    //     $existingProduct = Product::where('seo', $inputSeo)->where('id', '!=', $request->id)->first();
    //     if ($existingProduct) {
    //         return response()->json([
    //             'message' => 'SEO sudah digunakan oleh produk lain',
    //             'errors' => [
    //                 'merk' => ['Produk mungkin sudah ada'],
    //             ],
    //         ], 422);
    //     }

    //     $product->merk = $request->merk;
    //     $product->seo = $inputSeo;
    //     $product->sell_price = $validSellPrice;
    //     $product->save();

    //     return response()->json([
    //         'message' => 'Barang berhasil diedit'
    //     ]);
    // }

    public function delete(Request $request)
    {
        if (!$request->ajax()) return response()->json([
            'message' => 'Invalid request'
        ], 400);

        foreach ($request->id as $id) {
            $product = User::find($id);
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
