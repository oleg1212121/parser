<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['name', 'description','document','is_done','priority','published_at'];
    protected $table = 'orders';

    public function settings()
    {
        return $this->belongsToMany(Setting::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
