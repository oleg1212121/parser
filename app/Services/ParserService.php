<?php
/**
 * Created by PhpStorm.
 * User: Dimsa
 * Date: 24.05.2020
 * Time: 16:16
 */

namespace App\Services;

use App\Models\Page;

class ParserService
{
    public $outputLinks = [];
    public $outputNextLink = null;

    protected $page;
    protected $domen = 'https://market.yandex.by';
    protected $endForSpecification = '/spec?track=tabs';
    protected $endForDescription = '?track=tabs';

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function parsingLinks()
    {
        $dom = \phpQuery::newDocument($this->page->content);
        $this->outputNextLink = $this->searchingNextButton($dom);
        $this->outputLinks = $this->searchingCardItemLinks($dom);
        \phpQuery::unloadDocuments();
        $this->setCurrentPageIsDone();
        return $this;

    }

    protected function searchingNextButton($dom)
    {
        $link = $dom->find(".n-pager__button-next")->attr('href') ?? null;
        return $this->domen.$link;
    }

    protected function searchingCardItemLinks($dom)
    {
        $arr = [];
        $d = $dom->find('.i-bem.b-zone.b-spy-visible.b-spy-events h3 > a');

        foreach ($d as $item) {
            $i = pq($item)->attr('href');
            $i = preg_replace('/\?.*/', '', $i);
            array_push($arr, [
                'link' => $this->domen.$i.$this->endForSpecification,
                'type' => 1,
            ]);
        }

        return $arr;
    }

    protected function setCurrentPageIsDone()
    {
        $this->page->update(['is_done' => 1]);
    }

    protected function searchingCardItems($dom)
    {
//        $now = now();
//        $arr = [];
//        $cards = $dom->find(".n-snippet-card2");
//        $k = 0;
//        foreach ($cards as $item) {
//            $i = pq($item);
//
//            $a = $i->find('.n-snippet-card2__image img')->attr('src');
//
//            $title = $i->find('.n-snippet-card2__title a')->text();
//            $price = $i->find('.price')->text();
//            $rating = $i->find('.n-rating-stars')->attr('data-rate');
//            $review = $i->find('.n-shop-rating__description-count')->text();
//
//            $arr[$k] = [
//                'link' => $a,
//                'title' => $title,
//                'price' => $price,
//                'rating' => $rating,
//                'review' => $review,
//                'created_at' => $now,
//                'updated_at' => $now,
//            ];
//            $k++;
//        }
//
//        return $arr;
    }
}