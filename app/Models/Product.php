<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['title', 'link', 'market_id', 'content'];
    protected $table = 'products';

    public function images()
    {
        return $this->belongsToMany(Image::class);
    }
    public function links()
    {
        return $this->belongsTo(Link::class, 'link');
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
