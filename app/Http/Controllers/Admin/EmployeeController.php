<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\WzExcel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\MuwizaTable;
use App\Models\Access;
use App\Models\SalesTeam;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $data['employees'] = User::where('access_id', '>', 2)->orderBy('created_at', 'desc')->get();
        $table = $this->generateTable($data['employees']);
        if ($request->ajax()) {
            $rows = $table->result();
            return response()->json($rows);
        }
        $data['rows'] = $table->resultHTML();
        $data['positions'] = Access::where('id', '>', 2)->orderBy('id', 'desc')->get();
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
            'phone' => ['required'],
            'password' => ['required'],
            'email' => ['required'],
            'nik' => ['required'],
            'photo' => 'image|mimes:jpeg,png,jpg|max:5120',
            'position' => ['required'],
        ], [
            'name.required' => 'Nama karyawan harus diisi',
            'phone.required' => 'Nomor ponsel harus diisi',
            'phone.unique' => 'Nomor ponsel telah digunakan ',
            'password.required' => 'Password harus diisi',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email telah digunakan',
            'position.required' => 'Jabatan harus dipilih',
            'nik.required' => 'NIK harus diisi',
            'photo.image' => 'Foto harus gambar',
            'photo.mimes' => 'Ekstensi foto harus antara jpeg, png, atau jpg',
            'photo.max' => 'Ukuran file foto maksimal: 5 Mb',
        ]);

        // Validate, no and email
        if (User::where('phone', $request->phone)->exists()) {
            return response()->json([
                'message' => 'Nomor ponsel telah digunakan',
                'errors' => [
                    'phone' => ['Nomor ponsel telah digunakan'],
                ],
            ], 422);
        }
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'Email telah digunakan',
                'errors' => [
                    'email' => ['Email telah digunakan'],
                ],
            ], 422);
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

        return response()->json([
            'message' => 'Karyawan ditambahkan',
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
            'id' => $employee->id,
            'name' => $employee->name,
            'phone' => $employee->phone,
            'nik' => $employee->nik,
            'photo' => $employee->photo,
            'email' => $employee->email,
            'position' => $employee->access->name,
            'position_id' => $employee->access_id,
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

    public function update(Request $request)
    {
        try {
            $employee = User::findOrFail($request->id);
        } catch (ModelNotFoundException $th) {
            return response()->json([
                'message' => 'Karyawan tidak ditemukan'
            ], 404);
        }
        $request->validate([
            'name' => ['required'],
            'phone' => ['required'],
            'email' => ['required'],
            'nik' => ['required'],
            'photo' => 'image|mimes:jpeg,png,jpg|max:5120',
            'position' => ['required'],
        ], [
            'name.required' => 'Nama karyawan harus diisi',
            'phone.required' => 'Nomor ponsel harus diisi',
            'phone.unique' => 'Nomor ponsel telah digunakan ',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email telah digunakan',
            'position.required' => 'Jabatan harus dipilih',
            'nik.required' => 'NIK harus diisi',
            'photo.image' => 'Foto harus gambar',
            'photo.mimes' => 'Ekstensi foto harus antara jpeg, png, atau jpg',
            'photo.max' => 'Ukuran file foto maksimal: 5 Mb',
        ]);

        if (User::where('phone', $request->phone)->whereNot('id', $request->id)->exists()) {
            return response()->json([
                'message' => 'Nomor ponsel telah digunakan',
                'errors' => [
                    'phone' => ['Nomor ponsel telah digunakan'],
                ],
            ], 422);
        }
        if (User::where('email', $request->email)->whereNot('id', $request->id)->exists()) {
            return response()->json([
                'message' => 'Email telah digunakan',
                'errors' => [
                    'email' => ['Email telah digunakan'],
                ],
            ], 422);
        }

        $path = null;
        $dir = 'employees';
        if ($request->hasFile('photo')) {
            Storage::delete('public/' . $employee->photo);
            $path = $request->file('photo')->store("public/$dir");
            $employee->photo = $path ? ($dir . '/' . basename($path)) : null;
        }

        $employee->name = $request->name;
        if ($request->password) {
            $employee->password = bcrypt($request->password);
        }
        $employee->nik = $request->nik;
        $employee->phone = $request->phone;
        $employee->email = $request->email;
        $employee->access_id = $request->position;
        $employee->save();

        return response()->json([
            'message' => 'Karyawan diedit',
        ]);
    }

    public function delete(Request $request)
    {
        if (!$request->ajax()) return response()->json([
            'message' => 'Invalid request'
        ], 400);

        foreach ($request->id as $id) {
            $employee = User::whereIn('access_id', [3, 4])->find($id);
            if ($employee) {
                if ($employee->photo) {
                    Storage::delete('public/' . $employee->photo);
                }
                $employee->delete();
            } else {
                return response()->json([
                    'message' => 'Karyawan tidak ditemukan'
                ], 400);
            }
        }

        return response()->json([
            'message' => 'Karyawan berhasil dihapus',
        ]);
    }

    public function export()
    {
        $employees = User::where('access_id', '>', 2)->orderBy('created_at', 'desc')->get();
        $extract = ['name', 'phone', 'email', 'nik', 'position'];
        if (auth()->user()->access_id == 1) {
            $extract[] = 'photo';
        }
        $dataEmployees = MuwizaTable::generate($employees, function ($row, $cols) {
            $cols->position = $row->access->name;
            return $cols;
        })->extract($extract)
            ->withoutId()
            ->result();

        $excelHeadings = [
            'Nama', 'Nomor WhatsApp', 'Email', 'NIK', 'Jabatan'
        ];
        if (auth()->user()->access_id == 1) {
            $excelHeadings[] = 'Path Gambar';
        }
        $excel = new WzExcel('Data Karyawan', $excelHeadings, $dataEmployees);
        return Excel::download($excel, 'Daftar Karyawan.xlsx');
    }
}
