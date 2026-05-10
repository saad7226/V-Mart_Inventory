<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToStore;

class Purchase extends Model
{
    use HasFactory, BelongsToStore;

    protected $fillable = [
        'store_id',
        'supplier_id',
        'user_id',
        'sub_total',
        'tax',
        'discount_value',
        'discount_type',
        'shipping',
        'grand_total',
        'status',
        'date',
    ];
    protected $table = 'purchases';
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
}
