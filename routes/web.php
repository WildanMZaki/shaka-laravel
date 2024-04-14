<?php

use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ExpenditureController;
use App\Http\Controllers\Admin\KasbonController;
use App\Http\Controllers\Admin\MonthlyInsentiveController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\PresenceController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RestockController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\SallaryController;
use App\Http\Controllers\Admin\SallaryRuleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashController;
use App\Http\Controllers\Setting\MenuController;
use App\Http\Controllers\Setting\SubMenuController;
use App\Http\Controllers\TryController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Request;
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
    // Route::get('/run-queue-worker', function () {
    //     Artisan::call('queue:work');
    //     return 'Queue worker has been executed.';
    // });
    Route::get('/run-migrate', function () {
        $exitCode = Artisan::call('migrate');
        return 'Migration Completed.' . "($exitCode)";
    });
    Route::get('/optimize', function () {
        $exitCode = Artisan::call('optimize');
        $exitCode2 = Artisan::call('config:cache');
        $exitCode3 = Artisan::call('route:cache');
        $exitCode4 = Artisan::call('view:cache');
        return 'Optimization complete.' . "($exitCode)($exitCode2)($exitCode3)($exitCode4)";
    });
    Route::get('/run-seeder', function () {
        $exitCode = Artisan::call('db:seed --class=ReportMenuSeeder');
        return 'Seeding executed.' . "($exitCode)";
    });
    // Route::get('/run-recache', function () {
    //     $exitCode1 = Artisan::call('cache:clear');
    //     $exitCode2 = Artisan::call('config:cache');

    //     echo $exitCode1;
    //     echo '<br>';
    //     echo $exitCode2;
    // });
    // Route::get('/create-symlink', function () {
    //     $laravelPath = realpath($_SERVER['DOCUMENT_ROOT'] . "/../shakapratama.wize.my.id");
    //     $target = $laravelPath . "/storage/app/public";
    //     $link = $laravelPath . "/public/storage"; // Adjusted path

    //     echo $laravelPath;
    //     echo '<br>';
    //     echo $target;
    //     echo '<br>';
    //     echo $link;

    //     if (is_link($link)) {
    //         echo "Symbolic link already exists.";
    //     } elseif (symlink($target, $link)) {
    //         echo "Symbolic link created successfully.";
    //     } else {
    //         echo "Failed to create symbolic link.";
    //     }
    // });

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
            Route::patch('/bpjs', [EmployeeController::class, 'switch_bpjs'])->name('employee.switch_bpjs');
            Route::post('/import_data', [EmployeeController::class, 'import'])->name('employees.import');
            Route::post('/', [EmployeeController::class, 'store'])->name('employee.store');
            Route::put('/', [EmployeeController::class, 'update'])->name('employee.update');
            Route::get('/excel_data', [EmployeeController::class, 'export'])->name('employees.export');
            Route::get('/positions', [PositionController::class, 'index'])->name('employees.positions');
            Route::get('/{id}', [EmployeeController::class, 'detail'])->name('employee.detail');
            Route::delete('/', [EmployeeController::class, 'delete'])->name('employees.delete');
        });

        Route::prefix('presences')->group(function () {
            Route::get('/', [PresenceController::class, 'index'])->name('presences');
            Route::post('/', [PresenceController::class, 'manual'])->name('presences.sign_manual');
            Route::patch('/', [PresenceController::class, 'change'])->name('presences.change');
            Route::put('/all', [PresenceController::class, 'confirm_all'])->name('presences.confirm_all');
            Route::prefix('permits')->group(function () {
                Route::patch('/', [PresenceController::class, 'permit_change'])->name('presences.permits.change');
                Route::put('/all', [PresenceController::class, 'allow_all'])->name('presences.permits.allow_all');
            });
        });

        Route::prefix('sales')->group(function () {
            Route::get('/', [SalesController::class, 'index'])->name('sales');
            Route::post('/', [SalesController::class, 'store'])->name('sales.store');
            Route::delete('/', [SalesController::class, 'delete'])->name('sales.delete');
        });

        Route::prefix('expenditures')->group(function () {
            Route::get('/', [ExpenditureController::class, 'index'])->name('expenditures');
            Route::post('/', [ExpenditureController::class, 'store'])->name('expenditures.store');
            Route::delete('/', [ExpenditureController::class, 'delete'])->name('expenditures.delete');
        });

        Route::prefix('kasbons')->group(function () {
            Route::get('/', [KasbonController::class, 'index'])->name('kasbons');
            Route::post('/', [KasbonController::class, 'manual'])->name('kasbons.manual');
            Route::patch('/', [KasbonController::class, 'change_status'])->name('kasbons.change_status');
            Route::delete('/', [KasbonController::class, 'delete'])->name('kasbons.delete');
        });

        Route::prefix('sallaries')->group(function () {
            Route::prefix('rules')->group(function () {
                Route::get('/', [SallaryRuleController::class, 'index'])->name('sallaries.rules');
                Route::prefix('insentives')->group(function () {
                    Route::post('/', [SallaryRuleController::class, 'store_insentive'])->name('rules.insentives.store');
                    Route::put('/', [SallaryRuleController::class, 'update_insentive'])->name('rules.insentives.update');
                    Route::delete('{id}', [SallaryRuleController::class, 'delete_insentive'])->name('rules.insentives.delete');
                });
            });
            Route::prefix('monthly')->group(function () {
                Route::get('/', [MonthlyInsentiveController::class, 'index'])->name('sallaries.monthly');
                Route::post('/', [MonthlyInsentiveController::class, 'count'])->name('sallaries.monthly.generate');
            });
            Route::get('/', [SallaryController::class, 'index'])->name('sallaries.list');
            Route::get('/download/{weekly_sallary_id}', [SallaryController::class, 'download'])->name('sallaries.download');
            Route::post('/generate', [SallaryController::class, 'count_sallaries'])->name('sallaries.generate');
            Route::post('/recount', [SallaryController::class, 'recount'])->name('sallaries.recount');
            Route::get('/count_monitor', [SallaryController::class, 'monitor_counting'])->name('sallaries.monitor');
            Route::get('/{sallary_id}', [SallaryController::class, 'detail'])->name('sallaries.detail');
        });

        Route::prefix('reports')->group(function () {
            Route::get('presences', [ReportController::class, 'presences'])->name('reports.presences');
            Route::get('teams', [ReportController::class, 'teams'])->name('reports.teams');
            Route::get('sales', [ReportController::class, 'sales'])->name('reports.sales');
            Route::get('finance', [ReportController::class, 'finance'])->name('reports.finance');
        });

        Route::prefix('settings')->group(function () {
            Route::put('/change', [SettingController::class, 'change'])->name('settings.change');
        });
    });
});
