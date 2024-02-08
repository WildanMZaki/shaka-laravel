<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presence;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function index(Request $request)
    {
        $data['autoConfirm'] = Settings::of('Auto Konfirmasi Absensi');
        $data['presences'] = Presence::where('flag', 'hadir')
            ->whereDate('date', now())
            ->orderByRaw("FIELD(status, 'pending', 'rejected', 'approved')")
            ->orderBy('entry_at', 'asc')
            ->get();

        $data['permits'] = Presence::whereIn('flag', ['izin', 'sakit'])
            ->whereDate('date', now())
            ->orderByRaw("FIELD(status, 'pending', 'rejected', 'approved')")
            ->orderBy('entry_at', 'asc')
            ->get();
        $data['unpresences'] = User::whereDoesntHave('presences', function ($query) {
            $query->whereDate('date', now());
            $query->orderBy('entry_at', 'asc');
        })
            ->where('access_id', '>', 2)
            ->get();

        return view('admin.presences.index', $data);
    }

    public function confirm_all()
    {
        $affectedRows = Presence::whereDate('date', now())
            ->where('status', 'pending')
            ->where('flag', 'hadir')
            ->update(['status' => 'approved']);

        if ($affectedRows > 0) {
            $presences = Presence::with(['user.access', 'user' => function ($query) {
                $query->select('id', 'name', 'access_id');
            }])
                ->where('flag', 'hadir')
                ->whereDate('date', now())
                ->orderBy('entry_at', 'asc')
                ->get();
            return response()->json([
                'message' => 'Semua Kehadiran Dikonfirmasi',
                'data' => [
                    'presences' => $presences
                ],
            ]);
        } else {
            return response()->json([
                'message' => 'Terjadi Kesalahan',
            ], 500);
        }
    }

    public function change(Request $request)
    {
        try {
            $presence = Presence::findOrFail($request->id);
        } catch (ModelNotFoundException $th) {
            return response()->json([
                'message' => 'Data kehadiran tidak ditemukan',
            ], 404);
        }

        $presence->status = $request->status;
        if ($presence->save()) {
            $presences = Presence::with(['user.access', 'user' => function ($query) {
                $query->select('id', 'name', 'access_id');
            }])
                ->where('flag', 'hadir')
                ->whereDate('date', now())
                ->orderBy('entry_at', 'asc')
                ->get();
            return response()->json([
                'message' => 'Kehadiran ' . ($request->status == 'approved' ? 'Dikonfirmasi' : 'Ditolak'),
                'data' => [
                    'presences' => $presences
                ],
            ]);
        } else {
            return response()->json([
                'message' => 'Terjadi Kesalahan',
            ], 500);
        }
    }

    public function allow_all()
    {
        $affectedRows = Presence::whereDate('date', now())
            ->where('status', 'pending')
            ->where('flag', '!=', 'hadir')
            ->update(['status' => 'approved']);

        if ($affectedRows > 0) {
            $permits = Presence::with(['user.access', 'user' => function ($query) {
                $query->select('id', 'name', 'access_id');
            }])
                ->where('flag', '!=', 'hadir')
                ->whereDate('date', now())
                ->orderBy('entry_at', 'asc')
                ->get();
            return response()->json([
                'message' => 'Semua permintaan izin disetujui',
                'data' => [
                    'permits' => $permits
                ],
            ]);
        } else {
            return response()->json([
                'message' => 'Terjadi Kesalahan',
            ], 500);
        }
    }

    public function permit_change(Request $request)
    {
        try {
            $permit = Presence::findOrFail($request->id);
        } catch (ModelNotFoundException $th) {
            return response()->json([
                'message' => 'Data kehadiran tidak ditemukan',
            ], 404);
        }

        $permit->status = $request->status;
        if ($permit->save()) {
            $permits = Presence::with(['user.access', 'user' => function ($query) {
                $query->select('id', 'name', 'access_id');
            }])
                ->whereIn('flag', ['izin', 'sakit'])
                ->whereDate('date', now())
                ->orderBy('entry_at', 'asc')
                ->get();
            return response()->json([
                'message' => 'Permohonan ' . $permit->flag . ' ' . ($request->status == 'approved' ? 'Disetujui' : 'Ditolak'),
                'data' => [
                    'permits' => $permits
                ],
            ]);
        } else {
            return response()->json([
                'message' => 'Terjadi Kesalahan',
            ], 500);
        }
    }

    public function manual(Request $request)
    {
        $request->validate([
            'user_id' => ['required'],
            'flag' => ['required'],
        ], [
            'user_id.required' => 'Diperlukan data pegawai',
            'flag.required' => 'Flag diperlukan',
        ]);

        $sign = new Presence();
        $sign->user_id = $request->user_id;
        $sign->flag = strtolower($request->flag);
        $sign->note = $request->flag;
        $sign->date = date('Y-m-d');
        $sign->entry_at = date('H:i:s');
        $sign->status = 'approved';
        $sign->photo = 'presences/default.png';
        $sign->save();

        return response()->json([
            'message' => 'Berhasil',
        ]);
    }
}
