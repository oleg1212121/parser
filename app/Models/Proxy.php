<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Proxy extends Model
{
    protected $table = 'proxies';
    protected $fillable = ['proxy','type','status','fails','used'];

    public static function scopeFreeProxy($query)
    {
        return $query->where('fails','<', 3)->orderBy('fails');
    }

    public function scopeAbandoned($query)
    {
        return $query->where('fails', '>', 2);
    }

    public function scopeForRestoring($query)
    {
        return $query->where('fails', '>', 0)->where('updated_at', '<', now()->addMinutes(-120)->format('Y-m-d H:m:s'));
    }
}
