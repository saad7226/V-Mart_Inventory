<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * ReportApiController
 *
 * PROTECTED (Android Seller — Bearer token required):
 *   GET /api/seller/reports/sales      → Sales report with date range
 *   GET /api/seller/reports/inventory  → Current stock levels + low-stock alerts
 *   GET /api/seller/reports/summary    → KPI dashboard cards (today/week/month)
 *
 * All data is scoped to the authenticated seller's store_id.
 */
class ReportApiController extends Controller
{
    // =========================================================================
    // SALES REPORT
    // =========================================================================

    /**
     * GET /api/seller/reports/sales
     *
     * Returns a detailed list of orders within a date range.
     * Android uses this for the "Sales Report" screen.
     *
     * Query Params:
     *   ?from=2025-01-01   (default: 30 days ago)
     *   ?to=2025-04-25     (default: today)
     *
     * Response:
     * {
     *   "success": true,
     *   "period": { "from": "Apr 01, 2025", "to": "Apr 25, 2025" },
     *   "summary": {
     *     "total_orders": 45,
     *     "sub_total": 12500.00,
     *     "total_discount": 750.00,
     *     "total_revenue": 11750.00,
     *     "total_collected": 11000.00,
     *     "total_due": 750.00
     *   },
     *   "orders": [ { "order_id": 1, "customer": "...", ... } ]
     * }
     */
    public function salesReport(Request $request)
    {
        try {
            $seller = $request->user();

            // Date range — default last 30 days
            $from = Carbon::parse($request->input('from', Carbon::today()->subDays(29)))->startOfDay();
            $to   = Carbon::parse($request->input('to',   Carbon::today()))->endOfDay();

            $orders = Order::with('customer')
                ->where('store_id', $seller->store_id)
                ->whereBetween('created_at', [$from, $to])
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'period'  => [
                    'from' => $from->format('M d, Y'),
                    'to'   => $to->format('M d, Y'),
                ],
                'summary' => [
                    'total_orders'    => $orders->count(),
                    'sub_total'       => (float) round($orders->sum('sub_total'), 2),
                    'total_discount'  => (float) round($orders->sum('discount'), 2),
                    'total_revenue'   => (float) round($orders->sum('total'), 2),
                    'total_collected' => (float) round($orders->sum('paid'), 2),
                    'total_due'       => (float) round($orders->sum('due'), 2),
                ],
                'orders' => $orders->map(fn($o) => [
                    'order_id'   => $o->id,
                    'customer'   => optional($o->customer)->name ?? 'Guest',
                    'sub_total'  => (float) $o->sub_total,
                    'discount'   => (float) $o->discount,
                    'total'      => (float) $o->total,
                    'paid'       => (float) $o->paid,
                    'due'        => (float) $o->due,
                    'status'     => $o->status ? 'paid' : 'due',
                    'date'       => $o->created_at?->format('M d, Y h:i A'),
                ]),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate sales report.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // =========================================================================
    // INVENTORY REPORT
    // =========================================================================

    /**
     * GET /api/seller/reports/inventory
     *
     * Returns current stock levels for all products in the seller's store.
     * Includes low-stock alerts — Android highlights items running low.
     *
     * Query Params:
     *   ?low_stock_threshold=10   (default: 10) — items below this are flagged
     *
     * Response:
     * {
     *   "success": true,
     *   "summary": {
     *     "total_products": 120,
     *     "active_products": 95,
     *     "out_of_stock": 5,
     *     "low_stock_count": 12,
     *     "total_stock_value": 58000.00
     *   },
     *   "low_stock_alerts": [ { "id": 3, "name": "...", "quantity": 2 } ],
     *   "products": [ { "id": 1, "name": "...", "quantity": 50, ... } ]
     * }
     */
    public function inventoryReport(Request $request)
    {
        try {
            $seller    = $request->user();
            $threshold = (int) $request->input('low_stock_threshold', 10);

            $products = Product::with(['category', 'unit'])
                ->where('store_id', $seller->store_id)
                ->latest()
                ->get();

            $activeProducts = $products->where('status', true);
            $outOfStock     = $products->where('quantity', 0);
            $lowStock       = $products->where('quantity', '>', 0)->where('quantity', '<=', $threshold);

            // Calculate total stock value (quantity × purchase_price)
            $totalStockValue = $products->sum(fn($p) => $p->quantity * $p->purchase_price);

            return response()->json([
                'success' => true,
                'summary' => [
                    'total_products'   => $products->count(),
                    'active_products'  => $activeProducts->count(),
                    'out_of_stock'     => $outOfStock->count(),
                    'low_stock_count'  => $lowStock->count(),
                    'total_stock_value'=> (float) round($totalStockValue, 2),
                ],
                'low_stock_alerts' => $lowStock->values()->map(fn($p) => [
                    'id'          => $p->id,
                    'name'        => $p->name,
                    'sku'         => $p->sku,
                    'quantity'    => (int) $p->quantity,
                    'unit'        => optional($p->unit)->short_name,
                    'image_url'   => $p->image ? url($p->image) : null,
                ]),
                'products' => $products->map(fn($p) => [
                    'id'              => $p->id,
                    'name'            => $p->name,
                    'sku'             => $p->sku,
                    'category'        => optional($p->category)->name,
                    'price'           => (float) $p->price,
                    'discounted_price'=> (float) $p->discounted_price,
                    'purchase_price'  => (float) $p->purchase_price,
                    'quantity'        => (int) $p->quantity,
                    'unit'            => optional($p->unit)->short_name,
                    'status'          => $p->status ? 'active' : 'inactive',
                    'stock_value'     => (float) round($p->quantity * $p->purchase_price, 2),
                    'image_url'       => $p->image ? url($p->image) : null,
                    'expire_date'     => $p->expire_date,
                ]),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate inventory report.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // =========================================================================
    // DASHBOARD SUMMARY (KPI Cards)
    // =========================================================================

    /**
     * GET /api/seller/reports/summary
     *
     * Returns KPI metrics for the Android Dashboard home screen.
     * Gives Today / This Week / This Month breakdowns.
     *
     * Response:
     * {
     *   "success": true,
     *   "today": {
     *     "orders": 5, "revenue": 1200.00, "collected": 1100.00
     *   },
     *   "this_week": { ... },
     *   "this_month": { ... },
     *   "all_time": { ... },
     *   "inventory": {
     *     "total_products": 120, "out_of_stock": 5, "low_stock": 12
     *   }
     * }
     */
    public function summary(Request $request)
    {
        try {
            $seller  = $request->user();
            $storeId = $seller->store_id;

            $today     = [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()];
            $thisWeek  = [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
            $thisMonth = [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];

            $calcPeriod = function (array $range) use ($storeId): array {
                $orders = Order::where('store_id', $storeId)
                    ->whereBetween('created_at', $range)
                    ->get();
                return [
                    'orders'    => $orders->count(),
                    'revenue'   => (float) round($orders->sum('total'), 2),
                    'collected' => (float) round($orders->sum('paid'), 2),
                    'due'       => (float) round($orders->sum('due'), 2),
                ];
            };

            $allOrders    = Order::where('store_id', $storeId)->get();
            $allProducts  = Product::where('store_id', $storeId)->get();
            $lowThreshold = 10;

            return response()->json([
                'success'    => true,
                'today'      => $calcPeriod($today),
                'this_week'  => $calcPeriod($thisWeek),
                'this_month' => $calcPeriod($thisMonth),
                'all_time'   => [
                    'orders'    => $allOrders->count(),
                    'revenue'   => (float) round($allOrders->sum('total'), 2),
                    'collected' => (float) round($allOrders->sum('paid'), 2),
                    'due'       => (float) round($allOrders->sum('due'), 2),
                ],
                'inventory' => [
                    'total_products'  => $allProducts->count(),
                    'active_products' => $allProducts->where('status', true)->count(),
                    'out_of_stock'    => $allProducts->where('quantity', 0)->count(),
                    'low_stock'       => $allProducts->where('quantity', '>', 0)
                                                     ->where('quantity', '<=', $lowThreshold)
                                                     ->count(),
                    'total_stock_value' => (float) round(
                        $allProducts->sum(fn($p) => $p->quantity * $p->purchase_price), 2
                    ),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate summary.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
