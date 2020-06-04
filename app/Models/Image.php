<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['name','link','is_done'];
    protected $table = 'images';

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
