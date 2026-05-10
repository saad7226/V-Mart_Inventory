<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToStore;

class OrderTransaction extends Model
{
    use HasFactory, BelongsToStore;
    protected $table = 'order_transactions';
    protected $fillable = ['store_id', 'amount', 'order_id', 'user_id', 'customer_id', 'paid_by'];
    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }
}
