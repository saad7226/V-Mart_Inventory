<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\InventoryApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ReportApiController;

/*
|--------------------------------------------------------------------------
| V-Mart VR Store — API Routes
|--------------------------------------------------------------------------
|
| Architecture:
|   Unity App (Customers)  → Public routes + POST /orders (no seller login needed)
|   Android App (Sellers)  → Protected routes under auth:sanctum
|
| Base URL: https://<your-cloudflare-tunnel>.trycloudflare.com/api/
|
*/

// ---------------------------------------------------------------------------
// AUTH ROUTES (Public — no token required)
// ---------------------------------------------------------------------------

// Seller login → returns Bearer token
Route::post('/login', [AuthApiController::class, 'login'])->name('api.login');


// ---------------------------------------------------------------------------
// PUBLIC ROUTES — Unity Customer (No Authentication Required)
// ---------------------------------------------------------------------------
// Unity fetches all active products to render 3D shelves
Route::get('/products', [InventoryApiController::class, 'index'])->name('api.products.index');

// Unity fetches single product details (price, description, image URL)
Route::get('/products/{id}', [InventoryApiController::class, 'show'])->name('api.products.show');

// Unity customer placing an order (checkout from VR store)
// No login required — guest checkout identified by customer name/phone
Route::post('/orders', [OrderApiController::class, 'store'])->name('api.orders.store');


// ---------------------------------------------------------------------------
// PROTECTED ROUTES — Android Seller App (Bearer Token Required)
// ---------------------------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {

    // --- Auth ---
    Route::post('/logout', [AuthApiController::class, 'logout'])->name('api.logout');
    Route::get('/me', [AuthApiController::class, 'me'])->name('api.me');

    // --- Seller Inventory Management ---
    // GET    /api/seller/products        → list all products in seller's store
    // POST   /api/seller/products        → add new product
    // PUT    /api/seller/products/{id}   → update product
    // DELETE /api/seller/products/{id}   → delete product
    Route::prefix('seller')->name('api.seller.')->group(function () {

        Route::get('/products', [InventoryApiController::class, 'sellerIndex'])->name('products.index');
        Route::post('/products', [InventoryApiController::class, 'sellerStore'])->name('products.store');
        Route::put('/products/{id}', [InventoryApiController::class, 'sellerUpdate'])->name('products.update');
        Route::delete('/products/{id}', [InventoryApiController::class, 'sellerDestroy'])->name('products.destroy');

        // --- Seller Order Management ---
        Route::get('/orders', [OrderApiController::class, 'sellerOrders'])->name('orders.index');
        Route::get('/orders/{id}', [OrderApiController::class, 'sellerOrderDetail'])->name('orders.show');

        // --- Seller Reports ---
        Route::get('/reports/sales', [ReportApiController::class, 'salesReport'])->name('reports.sales');
        Route::get('/reports/inventory', [ReportApiController::class, 'inventoryReport'])->name('reports.inventory');
        Route::get('/reports/summary', [ReportApiController::class, 'summary'])->name('reports.summary');

    });
});
