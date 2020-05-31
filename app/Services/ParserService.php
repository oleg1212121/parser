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
    public $product = [];

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
        return $this;
    }

    public function parsingProduct()
    {
        $dom = \phpQuery::newDocument($this->page->content);
        $this->product = $this->searchingProductContent($dom);
        \phpQuery::unloadDocuments();
        return $this;
    }

    protected function searchingNextButton($dom)
    {
        $link = $dom->find(".n-pager__button-next")->attr('href') ?? null;
        return $link ? $this->domen . $link : null;
    }

    protected function searchingCardItemLinks($dom)
    {
        $arr = [];
        $d = $dom->find('.i-bem.b-zone.b-spy-visible.b-spy-events h3 > a');

        foreach ($d as $item) {
            $i = pq($item)->attr('href');
            $link = $this->buildProductLink($i);
            if($link){
                array_push($arr, [
                    'link' => $link,
                    'type' => 1,
                ]);
            }
        }

        return $arr;
    }

    protected function buildProductLink($url)
    {
        $i = preg_replace('/\?.*/', '', $url);
        $redirect = preg_match('/\/redir\//', $i);
        return ($i && !$redirect) ? $this->domen . $i . $this->endForSpecification : '';
    }

    public function setCurrentPageIsDone()
    {
        $this->page->update(['is_done' => 1]);
        return $this;
    }

    protected function searchingProductContent($dom)
    {
        $now = now();
        $content = [];

        $image = 'https:' . $dom->find('._2hXkcuvR_J')->attr('src');
        $a = $dom->find('._27nuSZ19h7');
        $href = $a->attr('href');
        $productLink = $this->buildProductLink($href);
        $id = preg_replace(['/\/.*\//', '/(\?|\/).*/'], '', $href);
        $title = $a->find("h1")->text();
        $spec = $dom->find("dl");

        foreach ($spec as $item) {
            $i = pq($item);

            $attr = $i->find('dt div span')->text();

            $value = $i->find('dd')->text();
            if($attr && $value){
                $content[$attr] = $value;
            }
        }

        $arr = [
            'market_id' => $id,
            'title' => $title,
            'content' => json_encode($content),
            'link' => $productLink,
            'image' => $image,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        return $arr;
    }
}