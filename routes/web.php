<?php

use App\Http\Controllers\Backend\CurrencyController;
use App\Http\Controllers\Backend\Pos\CartController;
use App\Http\Controllers\Backend\Product\ProductController;
use App\Http\Controllers\Backend\Report\ReportController;
use App\Http\Controllers\Backend\SupplierController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Backend\Product\CategoryController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\RolePermission\PermissionController;
use App\Http\Controllers\Backend\Pos\OrderController;
use App\Http\Controllers\Backend\Product\BrandController;
use App\Http\Controllers\Backend\Product\PurchaseController;
use App\Http\Controllers\Backend\RolePermission\RoleController;
use App\Http\Controllers\Backend\Product\UnitController;
use App\Http\Controllers\Backend\UserManagementController;
use App\Http\Controllers\Backend\StoreManagementController;
use App\Http\Controllers\Backend\WebsiteSettingController;
use App\Models\Supplier;

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

// ====================== FRONTEND ======================

// homepage
Route::get('/', function () {
    return to_route('login');
})->name('frontend.home');

//authentication
Route::match(['get', 'post'], 'login', [AuthController::class, 'login'])->name('login');

Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::match(['get', 'post'], 'sign-up', [AuthController::class, 'register'])->name('signup');
Route::match(['get', 'post'], 'forget-password', [AuthController::class, 'forgetPassword'])->name('forget.password');
Route::match(['get', 'post'], 'new-password', [AuthController::class, 'newPassword'])->name('new.password');
Route::match(['get', 'post'], 'password-reset', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::get('resend-otp', [AuthController::class, 'resendOtp'])->name('resend.otp');

// google auth
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.handle.callback');

// ====================== /FRONTEND =====================

// ====================== BACKEND =======================

Route::prefix('admin')->as('backend.admin.')->middleware(['admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    // Super Admin: Store Management
    Route::get('stores', [StoreManagementController::class, 'index'])->name('stores.index');
    Route::delete('stores/{id}', [StoreManagementController::class, 'destroy'])->name('stores.destroy');
    Route::resource('products', ProductController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('purchase', PurchaseController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('products', ProductController::class);
    Route::resource('units', UnitController::class);
    Route::resource('currencies', CurrencyController::class);
    Route::match(['get', 'post'], 'import/products', [ProductController::class,'import'])->name('products.import');
    Route::get('currencies/default/{id}', [CurrencyController::class, 'setDefault'])->name('currencies.setDefault');
    Route::get('customers/orders/{id}', [CustomerController::class, 'orders'])->name('customers.orders');
    Route::get('purchase/products/{id}', [PurchaseController::class, 'purchaseProducts'])->name('purchase.products');
    Route::get('orders/invoice/{id}', [OrderController::class,'invoice'])->name('orders.invoice');
    Route::get('orders/pos-invoice/{id}', [OrderController::class, 'posInvoice'])->name('orders.pos-invoice');
    Route::get('orders/transactions/{id}', [OrderController::class, 'transactions'])->name('orders.transactions');
    Route::match(['get', 'post'], 'orders/due/collection/{id}', [OrderController::class, 'collection'])->name('due.collection');
    Route::get('collection/invoice/{id}', [OrderController::class, 'collectionInvoice'])->name('collectionInvoice');
    Route::resource('categories', CategoryController::class);
    //start report

    Route::get('/sale/summery', [ReportController::class, 'saleSummery'])->name('sale.summery');
    Route::get('/sale/report', [ReportController::class, 'saleReport'])->name('sale.report');
    Route::get('/inventory/report', [ReportController::class, 'inventoryReport'])->name('inventory.report');
    //end report
   // start pos
    Route::get('/get/products', [CartController::class, 'getProducts'])->name('getProducts');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/increment', [CartController::class, 'increment']);
    Route::put('/cart/decrement', [CartController::class, 'decrement']);
    Route::put('/cart/delete', [CartController::class, 'delete']);
    Route::put('/cart/empty', [CartController::class, 'empty']);
    Route::put('/order/create', [OrderController::class, 'store']);
    Route::get('/get/customers',[CustomerController::class,'getCustomers']);
    Route::post('/create/customers', [CustomerController::class, 'store']);
    //end pos
    Route::get('profile', [DashboardController::class, 'profile'])->name('profile');
    Route::post('profile/update', [AuthController::class, 'update'])->name('profile.update');

    // user management
    Route::prefix('users')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('users');
        Route::get('suspend/{id}/{status}', [UserManagementController::class, 'suspend'])->name('user.suspend');
        Route::get('approve/{id}/{status}', [UserManagementController::class, 'approve'])->name('user.approve');
        Route::match(['get', 'post'], 'create', [UserManagementController::class, 'create'])->name('user.create');
        Route::match(['get', 'post'], 'edit/{id}', [UserManagementController::class, 'edit'])->name('user.edit');
        Route::get('delete/{id}', [UserManagementController::class, 'delete'])->name('user.delete');
    });

    // settings
    Route::prefix('settings')->group(function () {
        // website settings
        Route::prefix('website')->group(function () {
            Route::controller(WebsiteSettingController::class)->prefix('general')->group(function () {
                Route::get('/', 'websiteGeneral')->name('settings.website.general');
                Route::post('update-info', 'websiteInfoUpdate')->name('settings.website.info.update');
                Route::post('update-contacts', 'websiteContactsUpdate')->name('settings.website.contacts.update');
                Route::post('update-social-links', 'websiteSocialLinkUpdate')->name('settings.website.social.link.update');
                Route::post('update-style-settings', 'websiteStyleSettingsUpdate')->name('settings.website.style.settings.update');
                Route::post('update-custom-css', 'websiteCustomCssUpdate')->name('settings.website.custom.css.update');
                Route::post('update-notification-settings', 'websiteNotificationSettingsUpdate')->name('settings.website.notification.settings.update');
                Route::post('update-website-status', 'websiteStatusUpdate')->name('settings.website.status.update');

                Route::post('update-invoice-settings', 'websiteInvoiceUpdate')->name('settings.website.invoice.update');
            });

            Route::controller(RoleController::class)->prefix('roles')->group(function () {
                Route::get('/', 'index')->name('roles');
                Route::post('create', 'store')->name('roles.create');
                Route::get('show/{id}', 'show')->name('roles.show');
                Route::put('update/{id}', 'update')->name('roles.update');
                Route::get('delete/{id}', 'destroy')->name('roles.delete');
                Route::post('role-permission/{id}', 'updatePermission')->name('update.role-permissions');
                Route::get('role-wise-permissions/{id?}', 'roleWisePermissions')->name('role-wise-permissions');
            });

            Route::controller(PermissionController::class)->prefix('permissions')->group(function () {
                Route::get('/', 'index')->name('permissions');
                Route::post('create', 'store')->name('permissions.store');
                // Route::get('show/{id}', 'show')->name('roles.show');
                Route::put('update/{id}', 'update')->name('permissions.update');
                Route::get('delete/{id}', 'destroy')->name('permissions.delete');
            });
        });
    });
});

// ====================== /BACKEND ======================

Route::get('clear-all', function () {
    Artisan::call('optimize:clear');
    return redirect()->back();
});

Route::get('storage-link', function () {
    Artisan::call('storage:link');
    return redirect()->back();
});

Route::get('test', [TestController::class, 'test'])->name('test');

// -----------------------------------------------------------------------
// DIAGNOSTIC: Check all users and their store_id status
// Usage: https://virtualmartinventory.42web.io/check-stores?token=qpos_secret_2026
// DELETE THIS ROUTE AFTER USE
// -----------------------------------------------------------------------
Route::get('check-stores', function (\Illuminate\Http\Request $request) {
    if ($request->query('token') !== 'qpos_secret_2026') {
        abort(403);
    }
    $users  = \Illuminate\Support\Facades\DB::table('users')->get(['id','name','email','store_id']);
    $stores = \Illuminate\Support\Facades\DB::table('stores')->get();
    $html   = '<pre style="background:#111;color:#0f0;padding:20px;font-size:14px;">';
    $html  .= "=== STORES TABLE ===\n";
    foreach ($stores as $s) {
        $html .= "  ID:{$s->id}  Name:{$s->name}  Owner:{$s->owner_id}\n";
    }
    $html .= "\n=== USERS TABLE ===\n";
    foreach ($users as $u) {
        $storeStatus = $u->store_id ? "store_id={$u->store_id} ✅" : "store_id=NULL ⚠ NEEDS FIX";
        $html .= "  ID:{$u->id}  {$u->email}  {$storeStatus}\n";
    }
    $html .= "\nIf any user shows 'NEEDS FIX', visit /fix-user-stores?token=qpos_secret_2026\n";
    $html .= '</pre>';
    return response($html);
})->name('check.stores');

// -----------------------------------------------------------------------
// FIX: Assign a store to every user that has store_id = NULL
// Usage: https://virtualmartinventory.42web.io/fix-user-stores?token=qpos_secret_2026
// DELETE THIS ROUTE AFTER USE
// -----------------------------------------------------------------------
Route::get('fix-user-stores', function (\Illuminate\Http\Request $request) {
    if ($request->query('token') !== 'qpos_secret_2026') {
        abort(403);
    }
    $log   = '';
    $users = \Illuminate\Support\Facades\DB::table('users')->whereNull('store_id')->get();

    if ($users->isEmpty()) {
        return response('<pre style="background:#111;color:#0f0;padding:20px;">✅ All users already have a store_id. Nothing to fix!</pre>');
    }

    foreach ($users as $user) {
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Create a store for this user
            $storeId = \Illuminate\Support\Facades\DB::table('stores')->insertGetId([
                'name'       => $user->name . "'s Store",
                'owner_id'   => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Link store back to user
            \Illuminate\Support\Facades\DB::table('users')
                ->where('id', $user->id)
                ->update(['store_id' => $storeId]);

            \Illuminate\Support\Facades\DB::commit();
            $log .= "✅ Created store #{$storeId} \"{$user->name}'s Store\" → assigned to user {$user->email}\n";
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            $log .= "❌ Failed for {$user->email}: {$e->getMessage()}\n";
        }
    }

    $log .= "\n🎉 Done! Visit /check-stores?token=qpos_secret_2026 to verify.\n";
    $log .= "⚠  Remember to DELETE these routes from routes/web.php after use!\n";
    return response('<pre style="background:#111;color:#0f0;padding:20px;">' . htmlspecialchars($log) . '</pre>');
})->name('fix.user.stores');

// -----------------------------------------------------------------------
// RUN MIGRATIONS VIA BROWSER (For InfinityFree Shared Hosting)
// Usage: https://virtualmartinventory.42web.io/run-migrations?token=qpos_secret_2026
// DELETE THIS ROUTE AFTER USE
// -----------------------------------------------------------------------
Route::get('run-migrations', function (\Illuminate\Http\Request $request) {
    if ($request->query('token') !== 'qpos_secret_2026') {
        abort(403);
    }
    
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output = \Illuminate\Support\Facades\Artisan::output();
        return response('<pre style="background:#111;color:#0f0;padding:20px;font-size:14px;">✅ Migrations Executed Successfully!\n\n' . htmlspecialchars($output) . '</pre>');
    } catch (\Exception $e) {
        return response('<pre style="background:#330000;color:#ff3333;padding:20px;font-size:14px;">❌ Migration Failed!\n\n' . htmlspecialchars($e->getMessage()) . '</pre>');
    }
})->name('run.migrations');

Route::get('show-logs', function (\Illuminate\Http\Request $request) {
    if ($request->query('token') !== 'qpos_secret_2026') {
        abort(403);
    }
    $logFile = storage_path('logs/laravel.log');
    if (!file_exists($logFile)) {
        return "No log file found.";
    }
    $logs = file_get_contents($logFile);
    // get the last 5000 characters
    $logs = substr($logs, -10000);
    return response('<pre style="background:#111;color:#0f0;padding:20px;font-size:14px;white-space:pre-wrap;">' . htmlspecialchars($logs) . '</pre>');
});
