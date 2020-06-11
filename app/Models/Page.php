<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['content', 'link_id','type', 'is_done', 'order_id'];
    protected $table = 'pages';

    /**
     * Тип страницы - категория
     * @var int
     */
    public static $CATEGORY_TYPE = 0;

    /**
     * Тип страницы - продукт - характеристики
     * @var int
     */
    public static $PRODUCT_TYPE_SPECIFICATION = 1;

    /**
     * Тип страницы - продукт - описание
     * @var int
     */
    public static $PRODUCT_TYPE_DESCRIPTION = 2;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function link()
    {
        return $this->belongsTo(Link::class);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeCategoryPagesReadyToProcess($query)
    {
        return $query->notDone()->whereType(self::$CATEGORY_TYPE);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeProductPagesReadyToProcess($query)
    {
        return $query->notDone()->whereType(self::$PRODUCT_TYPE_SPECIFICATION);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeProductDescriptionsReadyToProcess($query)
    {
        return $query->notDone()->whereType(self::$PRODUCT_TYPE_DESCRIPTION);
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

    /**
     * @param $query
     * @param $id
     * @return mixed
     */
    public function scopeForOrder($query, $id)
    {
        return $query->where('order_id', $id);
    }
}
