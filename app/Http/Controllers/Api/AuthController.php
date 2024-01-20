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
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();

        if (!$user || !$user->active) {
            return response()->json(['error' => 'Account is not active'], 401);
        }

        return response()->json(compact('token'));
    }

    public function logout()
    {
        Auth::logout();
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Berhasil Logout']);
    }
}
