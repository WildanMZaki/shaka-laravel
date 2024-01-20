<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presence;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
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
        $user_id = $request->attributes->get('user_id');

        $dir = 'presences';
        $path = $request->file('photo')->store("public/$dir");
        $fixPath = $dir . '/' . basename($path);

        $presence = new Presence();
        $presence->user_id = $user_id;
        $presence->date = now();
        $presence->entry_at = now();
        $presence->photo = $fixPath;
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
