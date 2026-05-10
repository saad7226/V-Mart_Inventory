<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToStore;

class Supplier extends Model
{
    use HasFactory, BelongsToStore;

    protected $fillable = ['store_id', 'name','phone', 'address'];
    protected $table = 'suppliers';
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
