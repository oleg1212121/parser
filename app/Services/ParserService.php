<?php
/**
 * Created by PhpStorm.
 * User: Dimsa
 * Date: 24.05.2020
 * Time: 16:16
 */

namespace App\Services;

use App\Models\Page;
use Carbon\Carbon;

class ParserService
{
    protected $page ;
    protected $res;

    public function __construct(Page $page)
    {
        $this->page = $page;

    }

    public function getRes()
    {
        return $this->res;
    }

    protected function parse()
    {
        $arr = [];
        $dom = \phpQuery::newDocument($this->page->content);

        $d = $dom->find(".n-snippet-card2");
        $k = 0;
        foreach ($d as $item){
            $i = pq($item);

            $a = $i->find('.n-snippet-card2__image img')->attr('src');

            $title = $i->find('.n-snippet-card2__title a')->text();
            $price = $i->find('.price')->text();
            $rating = $i->find('.n-rating-stars')->attr('data-rate');
            $review = $i->find('.n-shop-rating__description-count')->text();

            $arr[$k] = [
                'link' => $a,
                'title' => $title,
                'price' => $price,
                'rating' => $rating,
                'review' => $review,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
//            dd($arr[$k]);
            $k++;
        }

        \phpQuery::unloadDocuments();

        return $this;
    }

    public function parse2()
    {
        $arr = [];
        $dom = \phpQuery::newDocument($this->page->content);

        $d = $dom->find('.n-snippet-card2__header h3 a');

        $k = 0;
        foreach ($d as $item){
            $i = pq($item);
            $a = $i->attr('href');


            array_push($arr, $a);
            $k++;
        }

        \phpQuery::unloadDocuments();

        $this->res = $arr;

        return $this;
    }
}