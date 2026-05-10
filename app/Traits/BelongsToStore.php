<?php

namespace App\Traits;

use App\Models\Store;
use App\Scopes\StoreScope;
use Illuminate\Support\Facades\Auth;

trait BelongsToStore
{
    /**
     * The "booted" method of the trait.
     * Apply the StoreScope to all queries for this model.
     */
    protected static function bootBelongsToStore()
    {
        // Add the global scope so every query filters by store_id
        static::addGlobalScope(new StoreScope);

        // Hook into the creating event to automatically assign the store_id
        static::creating(function ($model) {
            // Only assign if the user is authenticated, has a store, and it's not already set
            if (Auth::check() && Auth::user()->store_id && empty($model->store_id)) {
                $model->store_id = Auth::user()->store_id;
            }
        });
    }

    /**
     * Relationship: This model belongs to aStore.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
