<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToStore;

class Brand extends Model
{
    use HasFactory, BelongsToStore;

    protected $guarded = [];
}
