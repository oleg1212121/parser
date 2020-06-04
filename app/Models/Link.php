<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $table = 'links';
    protected $fillable =  ['link', 'order_id', 'is_done', 'type','published_at'];

    /**
     * Тип ссылки - категория
     * @var int
     */
    public static $CATEGORY_TYPE = 0;

    /**
     * Тип ссылки - продукт - характеристики
     * @var int
     */
    public static $PRODUCT_TYPE_SPECIFICATION = 1;

    /**
     * Тип ссылки - продукт - описание
     * @var int
     */
    public static $PRODUCT_TYPE_DESCRIPTION = 2;

    /**
     * @param $query
     * @return mixed
     */
    public function scopeCategoryLinksReadyToProcess($query)
    {
        return $query->notDone()->whereType(self::$CATEGORY_TYPE);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeProductLinksReadyToProcess($query)
    {
        return $query->notDone()->whereIn('type',[self::$PRODUCT_TYPE_SPECIFICATION,self::$PRODUCT_TYPE_DESCRIPTION]);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeIsDone($query)
    {
        return $query->where('is_done',1);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeNotDone($query)
    {
        return $query->where('is_done',0);
    }
}
