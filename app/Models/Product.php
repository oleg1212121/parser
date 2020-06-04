<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['title', 'link', 'image', 'market_id', 'content'];
    protected $table = 'products';

    public function images()
    {
        return $this->belongsToMany(Image::class);
    }
}
