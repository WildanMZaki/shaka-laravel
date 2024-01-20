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
        return response()->json($data);
    }

    // Ini maksudnya mengajukan kasbon
    public function apply(Request $request)
    {
        $request->validate([
            'nominal' => 'required',
        ], [
            'nominal.required' => 'Nominal harus diisi',
        ]);
        $user_id = $request->attributes->get('user_id');

        $limitLeft = Kasbon::of($user_id);
        if ($request->nominal > $limitLeft) {
            return response()->json([
                'message' => 'Limit kasbon tercapai',
                'errors' => [
                    'nominal' => ['Nominal melebihi maksimal batas yang diizinkan'],
                ],
                'maxAllowed' => $limitLeft,
            ], 422);
        }

        $kasbon = new Kasbon();
        $kasbon->user_id = $user_id;
        $kasbon->kasbon_date = now();
        $kasbon->nominal = $request->nominal;
        $kasbon->note = $request->note;
        $kasbon->save();

        return response()->json([
            'message' => 'Pengajuan kasbon berhasil dibuat'
        ]);
    }
}
