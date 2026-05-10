<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * InventoryApiController
 *
 * PUBLIC (Unity Customer — no token):
 *   GET  /api/products       → All active & stocked products for 3D shelves
 *   GET  /api/products/{id}  → Single product detail panel in VR
 *
 * PROTECTED (Android Seller — Bearer token):
 *   GET    /api/seller/products        → Seller's store product list
 *   POST   /api/seller/products        → Add new product
 *   PUT    /api/seller/products/{id}   → Update product
 *   DELETE /api/seller/products/{id}   → Delete product
 */
class InventoryApiController extends Controller
{
    // =========================================================================
    // UNITY (PUBLIC) ENDPOINTS
    // =========================================================================

    /**
     * GET /api/products
     *
     * Returns all ACTIVE and IN-STOCK products across all stores.
     * Unity uses this to populate 3D shelves with product meshes.
     *
     * Supports optional query params:
     *   ?category_id=3    → filter by category
     *   ?search=shirt     → search by name or SKU
     *   ?per_page=20      → paginate (default: all)
     *
     * Unity Response Shape:
     * {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1, "name": "Red T-Shirt", "sku": "TS-001",
     *       "price": 25.00, "discounted_price": 20.00,
     *       "quantity": 50, "image_url": "https://tunnel.com/storage/...",
     *       "category": { "id": 2, "name": "Clothing" },
     *       "brand":    { "id": 1, "name": "Nike" },
     *       "unit":     { "id": 1, "name": "Piece", "short_name": "pc" }
     *     }
     *   ],
     *   "total": 42
     * }
     */
    public function index(Request $request)
    {
        try {
            $query = Product::with(['category', 'brand', 'unit'])
                ->active()   // scope: status = 1
                ->stocked(); // scope: quantity >= 1

            // Optional: filter by category
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Optional: search by name or SKU
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('sku', 'LIKE', "%{$search}%");
                });
            }

            $products = $query->latest()->get();

            return response()->json([
                'success' => true,
                'data'    => $products->map(fn($p) => $this->formatProduct($p)),
                'total'   => $products->count(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * GET /api/products/{id}
     *
     * Returns detailed info for a single product.
     * Unity shows this when the customer clicks/grabs an item in VR.
     *
     * Response adds: description, expire_date, discount info.
     */
    public function show($id)
    {
        try {
            $product = Product::with(['category', 'brand', 'unit'])
                ->active()
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $this->formatProductDetail($product),
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch product.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // =========================================================================
    // ANDROID SELLER ENDPOINTS (auth:sanctum)
    // =========================================================================

    /**
     * GET /api/seller/products
     *
     * Returns ALL products belonging to the authenticated seller's store.
     * Android shows this in the Seller Dashboard inventory list.
     *
     * Includes inactive and out-of-stock items (seller needs full visibility).
     */
    public function sellerIndex(Request $request)
    {
        try {
            $seller = $request->user();

            $products = Product::with(['category', 'brand', 'unit'])
                ->where('store_id', $seller->store_id)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'data'    => $products->map(fn($p) => $this->formatProductDetail($p)),
                'total'   => $products->count(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch seller products.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * POST /api/seller/products
     *
     * Seller adds a new product from the Android app.
     *
     * Android Request Body (JSON or multipart/form-data if sending image):
     * {
     *   "name": "Blue Jeans",
     *   "sku": "JN-202",
     *   "price": 45.00,
     *   "purchase_price": 30.00,
     *   "quantity": 100,
     *   "category_id": 2,
     *   "brand_id": 1,
     *   "unit_id": 1,
     *   "discount": 5,
     *   "discount_type": "percentage",   // or "fixed"
     *   "description": "Slim fit blue denim",
     *   "status": 1
     * }
     */
    public function sellerStore(Request $request)
    {
        try {
            $seller = $request->user();

            $validated = $request->validate([
                'name'           => 'required|string|max:255',
                'sku'            => 'required|string|max:100|unique:products,sku',
                'price'          => 'required|numeric|min:0',
                'purchase_price' => 'required|numeric|min:0',
                'quantity'       => 'required|integer|min:0',
                'category_id'    => 'required|exists:categories,id',
                'brand_id'       => 'nullable|exists:brands,id',
                'unit_id'        => 'nullable|exists:units,id',
                'discount'       => 'nullable|numeric|min:0',
                'discount_type'  => 'nullable|in:fixed,percentage',
                'description'    => 'nullable|string',
                'expire_date'    => 'nullable|date',
                'status'         => 'boolean',
            ]);

            $validated['store_id'] = $seller->store_id;

            $product = Product::create($validated);

            // Handle optional base64 image upload from Android
            if ($request->filled('image_base64')) {
                $path = $this->saveBase64Image($request->image_base64, $product->id);
                if ($path) {
                    $product->image = $path;
                    $product->save();
                }
            }

            $product->load(['category', 'brand', 'unit']);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'data'    => $this->formatProductDetail($product),
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * PUT /api/seller/products/{id}
     *
     * Seller updates an existing product from the Android app.
     * Only updates fields that are sent — partial updates supported.
     */
    public function sellerUpdate(Request $request, $id)
    {
        try {
            $seller = $request->user();

            // Scope to seller's store — prevents cross-store tampering
            $product = Product::where('id', $id)
                ->where('store_id', $seller->store_id)
                ->firstOrFail();

            $validated = $request->validate([
                'name'           => 'sometimes|string|max:255',
                'sku'            => 'sometimes|string|max:100|unique:products,sku,' . $product->id,
                'price'          => 'sometimes|numeric|min:0',
                'purchase_price' => 'sometimes|numeric|min:0',
                'quantity'       => 'sometimes|integer|min:0',
                'category_id'    => 'sometimes|exists:categories,id',
                'brand_id'       => 'nullable|exists:brands,id',
                'unit_id'        => 'nullable|exists:units,id',
                'discount'       => 'nullable|numeric|min:0',
                'discount_type'  => 'nullable|in:fixed,percentage',
                'description'    => 'nullable|string',
                'expire_date'    => 'nullable|date',
                'status'         => 'boolean',
            ]);

            $product->update($validated);

            // Handle optional base64 image update from Android
            if ($request->filled('image_base64')) {
                $path = $this->saveBase64Image($request->image_base64, $product->id);
                if ($path) {
                    $product->image = $path;
                    $product->save();
                }
            }

            $product->load(['category', 'brand', 'unit']);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'data'    => $this->formatProductDetail($product),
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or access denied.',
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * DELETE /api/seller/products/{id}
     *
     * Seller deletes a product. Scoped to their store only.
     */
    public function sellerDestroy(Request $request, $id)
    {
        try {
            $seller = $request->user();

            $product = Product::where('id', $id)
                ->where('store_id', $seller->store_id)
                ->firstOrFail();

            // Clean up image file if it exists
            if ($product->image && file_exists(public_path($product->image))) {
                @unlink(public_path($product->image));
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully.',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or access denied.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Format a product for Unity's 3D shelf rendering.
     * Returns only the fields Unity needs to display items.
     */
    private function formatProduct(Product $p): array
    {
        return [
            'id'               => $p->id,
            'name'             => $p->name,
            'slug'             => $p->slug,
            'sku'              => $p->sku,
            'price'            => (float) $p->price,
            'discounted_price' => (float) $p->discounted_price,
            'quantity'         => (int) $p->quantity,
            // Full URL so Unity's UnityWebRequest can download the texture
            'image_url'        => $p->image ? url($p->image) : null,
            'category'         => $p->category ? ['id' => $p->category->id, 'name' => $p->category->name] : null,
            'brand'            => $p->brand    ? ['id' => $p->brand->id,    'name' => $p->brand->name]    : null,
            'unit'             => $p->unit     ? ['id' => $p->unit->id,     'name' => $p->unit->name, 'short_name' => $p->unit->short_name] : null,
        ];
    }

    /**
     * Format a product with full details (for VR detail panel & seller dashboard).
     */
    private function formatProductDetail(Product $p): array
    {
        return array_merge($this->formatProduct($p), [
            'description'    => $p->description,
            'discount'       => (float) $p->discount,
            'discount_type'  => $p->discount_type,
            'purchase_price' => (float) $p->purchase_price,
            'expire_date'    => $p->expire_date,
            'status'         => (bool) $p->status,
            'store_id'       => $p->store_id,
            'created_at'     => $p->created_at?->toDateTimeString(),
            'updated_at'     => $p->updated_at?->toDateTimeString(),
        ]);
    }

    /**
     * Decode a base64 image string and save it to /public/media/products/.
     * Returns the relative path or null on failure.
     */
    private function saveBase64Image(string $base64, int $productId): ?string
    {
        try {
            // Strip data URI prefix if present: "data:image/jpeg;base64,..."
            if (str_contains($base64, ',')) {
                [, $base64] = explode(',', $base64);
            }

            $imageData = base64_decode($base64);
            if ($imageData === false) {
                return null;
            }

            $dir = public_path('media/products');
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $filename = 'product_' . $productId . '_' . time() . '.jpg';
            file_put_contents($dir . '/' . $filename, $imageData);

            return 'media/products/' . $filename;

        } catch (\Exception $e) {
            return null;
        }
    }
}
