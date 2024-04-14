<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
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
}
