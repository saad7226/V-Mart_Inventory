<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class StoreScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * Super Admin = ONLY user with id === 1.
     * No one else bypasses this scope — not even users with store_id = null.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Skip during artisan console commands (migrations, seeders, etc.)
        if (app()->runningInConsole()) {
            return;
        }

        // Skip on the users table — auth()->user() queries users, causing infinite recursion
        if ($model->getTable() === 'users') {
            return;
        }

        // Skip if no session/user (login page, public routes, etc.)
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();

        // ── SUPER ADMIN BYPASS ──────────────────────────────────────────────────
        // ONLY user with id = 1 is Super Admin. No other user bypasses this scope.
        if ($user->id === 1) {
            return;
        }

        // ── TENANT FILTER ───────────────────────────────────────────────────────
        if ($user->store_id) {
            $builder->where($model->getTable() . '.store_id', $user->store_id);
        } else {
            // User has no store and is not Super Admin → show nothing
            $builder->whereRaw('1 = 0');
        }
    }
}
