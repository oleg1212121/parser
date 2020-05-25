<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Proxy extends Model
{
    protected $table = 'proxies';
    protected $fillable = ['proxy','type','status','fails'];

    public static function getFreeProxy()
    {
        return self::where('fails','<', 3)->orderBy('fails')->first();
    }

    public function scopeAbandoned($query)
    {
        return $query->where('fails', '>', 2);
    }

    public function scopeForRestoring($query)
    {
        return $query->where('fails', '>', 2)->whereDate('updated_at', '>', now()->addMinutes(-60)->format('Y-m-d H:m:s'));
    }
}
