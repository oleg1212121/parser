<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['title', 'link', 'image', 'market_id'];
    protected $table = 'products';
}
