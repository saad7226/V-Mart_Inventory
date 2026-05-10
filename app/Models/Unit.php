<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToStore;

class Unit extends Model
{
    use HasFactory, BelongsToStore;
    protected $fillable = ['store_id','title','short_name'];
}
