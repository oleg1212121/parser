<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['content', 'link_id'];
    protected $table = 'pages';
}
