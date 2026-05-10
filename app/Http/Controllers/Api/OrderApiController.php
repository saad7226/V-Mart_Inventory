<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * OrderApiController
 *
 * PUBLIC (Unity Customer — no token):
 *   POST /api/orders → Unity guest checkout (VR cart → DB order)
 *
 * PROTECTED (Android Seller — Bearer token):
 *   GET /api/seller/orders       → List all orders for seller's store
 *   GET /api/seller/orders/{id}  → Detailed order with line items
 */
class OrderApiController extends Controller
{
    // =========================================================================
    // UNITY CUSTOMER CHECKOUT (PUBLIC — no auth required)
    // =========================================================================

    /**
     * POST /api/orders
     *
     * Unity VR Cart Checkout. The customer (guest) completes a purchase
     * inside the 3D VR store. No login required — identified by name/phone.
     *
     * Unity Request Body (JSON):
     * {
     *   "customer_name": "Ahmed Khan",       // required — for guest customer record
     *   "customer_phone": "03001234567",     // optional
     *   "customer_address": "Lahore, PK",    // optional
     *   "store_id": 1,                       // which store's products are being bought
     *   "payment_method": "cash",            // "cash" | "card" | "online"
     *   "paid": 150.00,                      // amount paid by customer
     *   "items": [
     *     { "product_id": 3, "quantity": 2 },
     *     { "product_id": 7, "quantity": 1 }
     *   ]
     * }
     *
     * Success Response (201):
     * {
     *   "success": true,
     *   "message": "Order placed successfully!",
     *   "data": {
     *     "order_id": 42,
     *     "total": 145.00,
     *     "paid": 150.00,
     *     "due": 0.00,
     *     "status": "paid",
     *     "items": [ ... ]
     *   }
     * }
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'customer_name'    => 'required|string|max:100',
                'customer_phone'   => 'nullable|string|max:20',
                'customer_address' => 'nullable|string|max:255',
                'store_id'         => 'required|exists:stores,id',
                'payment_method'   => 'nullable|in:cash,card,online',
                'paid'             => 'nullable|numeric|min:0',
                'items'            => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity'   => 'required|integer|min:1',
            ]);

            DB::beginTransaction();

            // 1. Find or create a guest customer record
            $customer = Customer::firstOrCreate(
                [
                    'store_id' => $request->store_id,
                    'phone'    => $request->customer_phone ?? 'VR-Guest',
                ],
                [
                    'name'     => $request->customer_name,
                    'address'  => $request->customer_address ?? '',
                ]
            );

            // 2. Create the Order shell
            $order = Order::create([
                'store_id'    => $request->store_id,
                'customer_id' => $customer->id,
                'user_id'     => null, // No seller logged in — VR guest order
            ]);

            $totalAmountOrder = 0;
            $orderItems       = [];
            $insufficientStock = [];

            // 3. Process each cart item
            foreach ($request->items as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->where('store_id', $request->store_id)
                    ->active()
                    ->lockForUpdate() // prevent race conditions on stock
                    ->first();

                if (! $product) {
                    $insufficientStock[] = "Product ID {$item['product_id']} not found in this store.";
                    continue;
                }

                if ($product->quantity < $item['quantity']) {
                    $insufficientStock[] = "'{$product->name}' only has {$product->quantity} units in stock.";
                    continue;
                }

                $mainTotal        = $product->price * $item['quantity'];
                $totalAfterDiscount = $product->discounted_price * $item['quantity'];
                $discount         = $mainTotal - $totalAfterDiscount;

                $totalAmountOrder += $totalAfterDiscount;

                // Create OrderProduct line item
                $orderProduct = $order->products()->create([
                    'store_id'       => $request->store_id,
                    'product_id'     => $product->id,
                    'quantity'       => $item['quantity'],
                    'price'          => $product->price,
                    'purchase_price' => $product->purchase_price,
                    'sub_total'      => $mainTotal,
                    'discount'       => $discount,
                    'total'          => $totalAfterDiscount,
                ]);

                // Reduce stock
                $product->decrement('quantity', $item['quantity']);

                $orderItems[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'quantity'     => $item['quantity'],
                    'unit_price'   => (float) $product->discounted_price,
                    'line_total'   => (float) $totalAfterDiscount,
                ];
            }

            // If ALL items failed stock check, rollback
            if (empty($orderItems)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Order failed — no items could be fulfilled.',
                    'errors'  => $insufficientStock,
                ], 422);
            }

            // 4. Finalise order totals
            $paid  = (float) ($request->paid ?? 0);
            $total = round($totalAmountOrder, 2);
            $due   = round($total - $paid, 2);

            $order->sub_total = $totalAmountOrder;
            $order->discount  = 0; // order-level discount not applied here
            $order->paid      = $paid;
            $order->total     = $total;
            $order->due       = $due;
            $order->status    = $due <= 0; // true = fully paid
            $order->save();

            // 5. Create payment transaction record
            if ($paid > 0) {
                $order->transactions()->create([
                    'store_id'    => $request->store_id,
                    'customer_id' => $customer->id,
                    'user_id'     => null,
                    'amount'      => $paid,
                    'paid_by'     => $request->payment_method ?? 'cash',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'data'    => [
                    'order_id' => $order->id,
                    'total'    => $total,
                    'paid'     => $paid,
                    'due'      => $due,
                    'status'   => $due <= 0 ? 'paid' : 'due',
                    'customer' => [
                        'id'    => $customer->id,
                        'name'  => $customer->name,
                        'phone' => $customer->phone,
                    ],
                    'items'          => $orderItems,
                    'stock_warnings' => $insufficientStock, // partial fulfilment warnings
                ],
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Order processing failed. Please try again.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // =========================================================================
    // ANDROID SELLER ENDPOINTS (auth:sanctum)
    // =========================================================================

    /**
     * GET /api/seller/orders
     *
     * Returns all orders for the seller's store, newest first.
     * Supports optional date filtering:
     *   ?from=2025-01-01&to=2025-12-31
     *   ?status=paid    or  ?status=due
     *
     * Android Response Shape:
     * {
     *   "success": true,
     *   "data": [
     *     {
     *       "order_id": 42, "customer": "Ahmed Khan", "total": 145.00,
     *       "paid": 150.00, "due": 0.00, "status": "paid",
     *       "total_items": 3, "created_at": "2025-04-20 14:30:00"
     *     }
     *   ],
     *   "total": 10
     * }
     */
    public function sellerOrders(Request $request)
    {
        try {
            $seller = $request->user();

            $query = Order::with('customer')
                ->where('store_id', $seller->store_id)
                ->latest();

            // Date range filter
            if ($request->filled('from')) {
                $query->whereDate('created_at', '>=', $request->from);
            }
            if ($request->filled('to')) {
                $query->whereDate('created_at', '<=', $request->to);
            }

            // Status filter: paid / due
            if ($request->filled('status')) {
                $query->where('status', $request->status === 'paid' ? 1 : 0);
            }

            $orders = $query->get();

            return response()->json([
                'success' => true,
                'data'    => $orders->map(fn($o) => $this->formatOrderSummary($o)),
                'total'   => $orders->count(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * GET /api/seller/orders/{id}
     *
     * Returns full order detail with all line items.
     * Android uses this for the order detail screen.
     */
    public function sellerOrderDetail(Request $request, $id)
    {
        try {
            $seller = $request->user();

            $order = Order::with(['customer', 'products.product', 'transactions'])
                ->where('store_id', $seller->store_id)
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => [
                    'order_id'     => $order->id,
                    'status'       => $order->status ? 'paid' : 'due',
                    'sub_total'    => (float) $order->sub_total,
                    'discount'     => (float) $order->discount,
                    'total'        => (float) $order->total,
                    'paid'         => (float) $order->paid,
                    'due'          => (float) $order->due,
                    'created_at'   => $order->created_at?->toDateTimeString(),
                    'customer'     => $order->customer ? [
                        'id'      => $order->customer->id,
                        'name'    => $order->customer->name,
                        'phone'   => $order->customer->phone,
                        'address' => $order->customer->address,
                    ] : null,
                    'items' => $order->products->map(fn($op) => [
                        'product_id'   => $op->product_id,
                        'product_name' => optional($op->product)->name,
                        'sku'          => optional($op->product)->sku,
                        'quantity'     => (int) $op->quantity,
                        'unit_price'   => (float) $op->price,
                        'discount'     => (float) $op->discount,
                        'total'        => (float) $op->total,
                    ]),
                    'transactions' => $order->transactions->map(fn($t) => [
                        'amount'  => (float) $t->amount,
                        'paid_by' => $t->paid_by,
                        'date'    => $t->created_at?->toDateTimeString(),
                    ]),
                ],
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function formatOrderSummary(Order $o): array
    {
        return [
            'order_id'    => $o->id,
            'customer'    => optional($o->customer)->name ?? 'Guest',
            'total_items' => (int) $o->total_item,
            'sub_total'   => (float) $o->sub_total,
            'discount'    => (float) $o->discount,
            'total'       => (float) $o->total,
            'paid'        => (float) $o->paid,
            'due'         => (float) $o->due,
            'status'      => $o->status ? 'paid' : 'due',
            'created_at'  => $o->created_at?->toDateTimeString(),
        ];
    }
}
