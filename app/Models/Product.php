<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['title', 'link', 'price', 'rating', 'review'];
    protected $table = 'products';
}
