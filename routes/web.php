<?php

use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RestockController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Setting\MenuController;
use App\Http\Controllers\Setting\SubMenuController;
use App\Http\Controllers\TryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('temp');
// });

// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'store'])->name('login');

Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('test', [TryController::class, 'debug']);

Route::prefix('/')->middleware(['auth'])->group(function () {
    Route::get('/', [DashController::class, 'index'])->name('dashboard');
    Route::prefix('dev-setting')->group(function () {
        Route::get('menu', [MenuController::class, 'index'])->name('settings.menus');
        Route::get('submenu', [SubMenuController::class, 'index'])->name('settings.sub_menus');
        Route::get('access', [SubMenuController::class, 'index'])->name('settings.access');
    });
    Route::prefix('admin')->group(function () {
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('products');
            Route::post('/', [ProductController::class, 'store'])->name('product.store');
            Route::patch('/', [ProductController::class, 'active_control'])->name('product.active_control');
            Route::put('/', [ProductController::class, 'update'])->name('product.update');

            Route::delete('/', [ProductController::class, 'delete'])->name('products.delete');
        });

        Route::prefix('restocks')->group(function () {
            Route::get('/', [RestockController::class, 'index'])->name('products.restocks.list');
            Route::get('/new', [RestockController::class, 'restock'])->name('products.restocks.form');
            Route::get('/{id}', [RestockController::class, 'detail'])->name('products.restocks.detail');
            Route::post('/', [RestockController::class, 'store'])->name('products.restock');
            Route::get('/change/{id}', [RestockController::class, 'edit'])->name('products.restocks.edit');
            Route::put('/', [RestockController::class, 'update'])->name('products.restocks.update');
            Route::delete('/', [RestockController::class, 'delete'])->name('products.restocks.delete');
        });

        Route::prefix('employees')->group(function () {
            Route::get('/', [EmployeeController::class, 'index'])->name('employees');
            Route::patch('/', [EmployeeController::class, 'active_control'])->name('employee.active_control');
            Route::post('/', [EmployeeController::class, 'store'])->name('employee.store');
            Route::get('/positions', [PositionController::class, 'index'])->name('employees.positions');
            Route::delete('/', [EmployeeController::class, 'delete'])->name('employees.delete');
        });
    });
});
