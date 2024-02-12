<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kasbon;
use Illuminate\Http\Request;

class KasbonController extends Controller
{
    public function index(Request $request)
    {
        $user_id = $request->attributes->get('user_id');
        $data = Kasbon::of($user_id, true);
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    // Ini maksudnya mengajukan kasbon
    public function apply(Request $request)
    {
        $request->validate([
            'nominal' => 'required',
            'type' => 'required',
        ], [
            'nominal.required' => 'Nominal harus diisi',
            'type.required' => 'Tipe kasbon harus dipilih',
        ]);
        $user_id = $request->attributes->get('user_id');

        $limitLeft = Kasbon::of($user_id);
        if ($request->nominal > $limitLeft) {
            return response()->json([
                'succes' => false,
                'message' => 'Limit kasbon tercapai',
                'errors' => [
                    'nominal' => ['Nominal melebihi maksimal batas yang diizinkan'],
                ],
                'maxAllowed' => $limitLeft,
            ], 422);
        }

        $kasbon = new Kasbon();
        $kasbon->user_id = $user_id;
        $kasbon->nominal = $request->nominal;
        $kasbon->type = $request->type;
        $kasbon->note = $request->note;
        $kasbon->save();

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan kasbon berhasil dibuat'
        ]);
    }
}
