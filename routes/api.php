<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KasbonController;
use App\Http\Controllers\Api\PresenceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [AuthController::class, 'login']);

Route::middleware('jwt')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('presences')->group(function () {
        Route::get('/', [PresenceController::class, 'check']);
        Route::post('/', [PresenceController::class, 'store']);
    });
    Route::prefix('kasbon')->group(function () {
        Route::get('/', [KasbonController::class, 'index']);
        Route::post('/', [KasbonController::class, 'apply']);
    });
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
