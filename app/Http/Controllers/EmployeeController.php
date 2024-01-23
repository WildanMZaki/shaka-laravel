<?php

namespace App\Http\Controllers;

use App\Helpers\MuwizaTable;
use App\Models\Access;
use App\Models\SalesTeam;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        $data['team_leaders'] = User::where('access_id', 3)->get();
        $data['positions'] = Access::whereIn('id', [3, 4])->orderBy('id', (count($data['team_leaders']) ? 'desc' : 'asc'))->get();
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
                unset($btns[$row->active ? 'activate' : 'inactivate']);
                $used = $row->active ? 'inactivate' : 'activate';
                $btns[$used]['classIcon'] = $row->active ? 'ti ti-user-off' : 'ti ti-user-check';
                return $btns;
            });
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'phone' => ['required', 'unique:users,phone'],
            'password' => ['required'],
            'email' => ['required', 'unique:users,email'],
            'nik' => ['required'],
            'photo' => 'image|mimes:jpeg,png,jpg|max:5120',
            'position' => ['required', 'in:3,4'],
        ], [
            'name.required' => 'Nama karyawan harus diisi',
            'phone.required' => 'Nomor ponsel harus diisi',
            'phone.unique' => 'Nomor ponsel telah digunakan ',
            'password.required' => 'Password harus diisi',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email telah digunakan',
            'position.required' => 'Jabatan harus dipilih',
            'position.in' => 'Jabatan tidak valid',
            'nik.required' => 'NIK harus diisi',
            'photo.image' => 'Foto harus gambar',
            'photo.mimes' => 'Ekstensi foto harus antara jpeg, png, atau jpg',
            'photo.max' => 'Ukuran file foto maksimal: 5 Mb',
        ]);

        if ($request->position == 4) {
            if (!$request->tl_id) {
                return response()->json([
                    'message' => 'Team Leader harus dipilih',
                    'errors' => [
                        'tl_id' => ['Team Leader harus dipilih'],
                    ],
                ], 422);
            } else {
                $salesTeam = new SalesTeam();
                $salesTeam->leader_id = $request->tl_id;
            }
        }

        $path = null;
        $dir = 'employees';
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store("public/$dir");
        }

        $employee = new User();
        $employee->name = $request->name;
        $employee->password = bcrypt($request->password);
        $employee->nik = $request->nik;
        $employee->photo = $path ? ($dir . '/' . basename($path)) : null;
        $employee->phone = $request->phone;
        $employee->email = $request->email;
        $employee->access_id = $request->position;
        $employee->save();

        if ($employee->access_id == 4) {
            $salesTeam->sales_id = $employee->id;
            $salesTeam->save();
        }

        return response()->json([
            'message' => 'Karyawan ditambahkan',
            'leaders' => User::where('active', 1)->where('access_id', 3)->get(['id', 'name']),
        ]);
    }

    public function detail(Request $request, $id)
    {
        if (!$request->ajax()) {
            return response()->json([
                'message' => 'Invalid Request!',
            ], 400);
        }
        try {
            $employee = User::findOrFail($id);
        } catch (ModelNotFoundException $th) {
            return response()->json([
                'message' => 'Karyawan tidak ditemukan',
            ], 400);
        }
        return response()->json([
            'name' => $employee->name,
            'phone' => $employee->phone,
            'nik' => $employee->nik,
            'photo' => $employee->photo,
            'email' => $employee->email,
            'position' => $employee->access->name,
            'leader' => $employee->access_id == 4 ? $employee->leader[0]->name : '-',
        ]);
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
            $employee = User::whereIn('access_id', [3, 4])->find($id);
            if ($employee) {
                $employee->delete();
            } else {
                return response()->json([
                    'message' => 'Karyawan tidak ditemukan'
                ], 400);
            }
        }

        return response()->json([
            'message' => 'Karyawan berhasil dihapus',
            'leaders' => User::where('active', 1)->where('access_id', 3)->get(['id', 'name']),
        ]);
    }
}
