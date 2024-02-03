<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $request->attributes->add([
                'user' => $user,
                'user_id' => $user->id,
                'access_id' => $user->access_id
            ]);
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['success' => false, 'message' => 'Token tidak valid'], 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['success' => false, 'message' => 'Token otorisasi telah kadaluwarsa'], 401);
            } else {
                return response()->json(['success' => false, 'message' => 'Token otorisasi tidak ditemukan'], 401);
            }
        }
        return $next($request);
    }
}
