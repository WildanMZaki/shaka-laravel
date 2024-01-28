<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presence;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PresenceController extends Controller
{
    public function check(Request $request)
    {
        $user_id = $request->attributes->get('user_id');

        $presence = Presence::whereDate('date', now())
            ->where('user_id', $user_id)
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => $presence,
        ]);
    }

    public function store(Request $request)
    {
        $user_id = $request->attributes->get('user_id');

        if (Presence::where('user_id', $user_id)->whereDate('date', now())->exists()) {
            return response()->json([
                'message' => 'Kamu sudah absen hari ini'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'flag' => 'required|in:hadir,sakit,izin',
        ], [
            'flag.required' => 'Flag absensi harus harus ada',
            'flag.in' => 'Flag absensi harus salah satu dari: hadir, sakit, dan izin',
            'photo.required' => 'Foto harus diupload',
            'photo.image' => 'Foto harus gambar',
            'photo.mimes' => 'Ekstensi foto harus antara jpeg, png, atau jpg',
            'photo.max' => 'Ukuran file foto maksimal: 5 Mb',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $dir = 'presences';
        $path = $request->file('photo')->store("public/$dir");
        $fixPath = $dir . '/' . basename($path);

        $presence = new Presence();
        $presence->user_id = $user_id;
        $presence->date = now();
        $presence->entry_at = now();
        $presence->photo = $fixPath;
        $presence->status = Settings::of('Auto Konfirmasi Absensi') ? 'approved' : 'pending';
        $presence->flag = $request->flag;
        $presence->note = $request->note;
        $presence->save();

        return response()->json([
            'message' => $request->flag == 'hadir'
                ? 'Absen berhasil'
                : "Permohonan {$request->flag} berhasil dibuat",
        ]);
    }
}
