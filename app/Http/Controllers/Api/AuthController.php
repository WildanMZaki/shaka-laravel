<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('phone', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Kredensial Salah',
            ], 401);
        }

        $user = Auth::user();

        if (!$user || !$user->active) {
            return response()->json(['success' => false, 'message' => 'Account is not active'], 401);
        }

        $resp['success'] = true;
        $resp['token'] = $token;
        $resp['name'] = $user->name;
        $resp['access_id'] = $user->access_id;
        $resp['access'] = $user->access->name;
        $resp['photo'] = $user->photoPath();

        return response()->json($resp);
    }

    public function logout()
    {
        Auth::logout();
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['success' => true, 'message' => 'Berhasil Logout']);
    }
}
