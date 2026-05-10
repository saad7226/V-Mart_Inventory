<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToStore;

class Customer extends Model
{
    use HasFactory, BelongsToStore;

    protected $fillable = ['store_id', 'name', 'phone', 'address'];
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
