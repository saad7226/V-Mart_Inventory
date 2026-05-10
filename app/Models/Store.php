<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_id',
        'address',
        'contact_info',
    ];

    /**
     * Get the user that owns the store.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get all users that belong to this store.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Add other hasMany relationships here if needed, like:
    // public function products() { return $this->hasMany(Product::class); }
}
