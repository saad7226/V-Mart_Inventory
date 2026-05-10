<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToStore;

class Category extends Model
{
    use HasFactory, BelongsToStore;

    protected $guarded = [];
}
