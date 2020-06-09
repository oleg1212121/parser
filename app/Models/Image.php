<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['name','link','is_done', 'extention'];
    protected $table = 'images';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeNotDone($query)
    {
        return $query->where('is_done', 0);
    }
}
