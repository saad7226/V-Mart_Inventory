<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreManagementController extends Controller
{
    /**
     * Super Admin = user with id=1 OR store_id=NULL (original admin before multi-tenancy).
     * These users bypass StoreScope and can see all data across all stores.
     */
    private function isSuperAdmin(): bool
    {
        $user = auth()->user();
        return $user->id === 1 || $user->store_id === null;
    }

    /**
     * Display all stores (Super Admin only).
     */
    public function index(Request $request)
    {
        abort_if(!$this->isSuperAdmin(), 403, 'Access denied. Super Admin only.');

        // Using DB::table to bypass ALL scopes and see every store
        $stores = DB::table('stores')
            ->leftJoin('users', 'stores.owner_id', '=', 'users.id')
            ->select(
                'stores.id',
                'stores.name as store_name',
                'stores.address',
                'stores.contact_info',
                'stores.created_at',
                'users.name as owner_name',
                'users.email as owner_email',
                DB::raw('(SELECT COUNT(*) FROM products WHERE products.store_id = stores.id) as product_count'),
                DB::raw('(SELECT COUNT(*) FROM orders WHERE orders.store_id = stores.id) as order_count'),
                DB::raw('(SELECT COUNT(*) FROM users u2 WHERE u2.store_id = stores.id) as user_count')
            )
            ->orderBy('stores.id', 'desc')
            ->get();

        return view('backend.stores.index', compact('stores'));
    }

    /**
     * Delete a store and all its associated data.
     */
    public function destroy($id)
    {
        if (auth()->id() !== 1 && auth()->user()->store_id !== null) {
            abort(403, 'Access denied. Super Admin only.');
        }

        DB::beginTransaction();
        try {
            // Nullify store_id on users (don't delete users)
            DB::table('users')->where('store_id', $id)->update(['store_id' => null]);

            // Delete the store
            DB::table('stores')->where('id', $id)->delete();

            DB::commit();
            return back()->with('success', 'Store deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete store: ' . $e->getMessage());
        }
    }
}
