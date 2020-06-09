<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['slug','name'];

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
