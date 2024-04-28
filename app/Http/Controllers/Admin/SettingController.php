<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function change(Request $request)
    {
        try {
            $setting = Settings::where('rule', trim($request->rule))->firstOrFail();
        } catch (ModelNotFoundException $th) {
            return response()->json([
                'message' => 'Setting tidak ditemukan',
            ], 404);
        }

        // Validation here:
        $type = $setting->type;
        if ($type == 'bool') {
            $allowed = [true, false, 0, 1];
            if (!in_array(trim($request->value), $allowed)) {
                return response()->json([
                    'message' => 'Value harus boolean',
                ], 400);
            }
            $value = trim($request->value);
        } else if ($type == 'int') {
            if (!ctype_digit(trim($request->value))) {
                return response()->json([
                    'message' => 'Value harus integer',
                ], 400);
            }
            $value = intval(trim($request->value));
        } else if ($type == 'json') {
            $value = json_encode(trim($request->value));
            if (!$value) {
                return response()->json([
                    'message' => 'Value harus objek atau array',
                ], 400);
            }
        } else {
            $value = trim($request->value);
        }

        $setting->value = $value;
        $setting->save();

        return response()->json([
            'message' => 'Setting berhasil diubah',
        ]);
    }

    public function get_profile(Request $request)
    {
        if (!$request->ajax()) return response()->json(['status' => 'error', 'message' => 'Invalid request'], 400);

        $id = auth()->user()->id;

        $user = User::find($id);
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        return response()->json([
            'status' => 'success',
            'data' => [
                'name' => $user->name,
                'phone' => $user->phone,
            ],
        ], 200);
    }

    public function change_profile(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|min:10|max:13',
        ], [
            'name.required' => 'Nama harus diisi',
            'phone.required' => 'Nomor ponsel harus diisi',
            'phone.min' => 'Nomor ponsel setidaknya harus 10 karakter',
            'phone.max' => 'Nomor ponsel maksimal sebanyak 13 karakter',
        ]);

        $userId = auth()->user()->id;
        if (User::where('phone', $request->phone)->whereNot('id', $userId)->exists()) {
            return response()->json([
                'message' => 'Nomor ponsel telah digunakan',
                'errors' => [
                    'phone' => ['Nomor ponsel telah digunakan'],
                ],
            ], 422);
        }

        $user = User::find($userId);
        $user->name = $request->name;
        $user->phone = $request->phone;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Profile berhasil diubah', 'name' => $user->name], 200);
    }
}
