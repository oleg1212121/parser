<?php
/**
 * Created by PhpStorm.
 * User: Dimsa
 * Date: 24.05.2020
 * Time: 16:16
 */

namespace App\Services;

use App\Models\Link;
use App\Models\Page;

class ParserService
{
    /**
     * Идентификатор продукта в маркете
     * @var null
     */
    public $productMarketId = null;

    /**
     * Массив ссылок на продукты (парсится со страниц категорий)
     * @var array
     */
    public $outputLinks = [];

//    /**
//     * Ссылка на следующую страницу категории (по пагинации)
//     * @var null
//     */
//    public $outputNextLink = null;

    /**
     * Массив данных для создания продукта в БД (парсится со страниц продукта)
     * @var array
     */
    public $product = [];

    /**
     * Массив изображений относящихся к продукту (только ссылки)
     * @var array
     */
    public $images = [];

    /**
     * Страница для парсинга
     * @var Page
     */
    protected $page;

    /**
     * Ссылка текущей страницы
     * @var Link
     */
    protected $link;

    /**
     * Url сайта
     * @var string
     */
    protected $domen = 'https://market.yandex.by';

    /**
     * Параметры для вкладки характеристик продукта
     * @var string
     */
    protected $endForSpecification = '/spec?track=tabs';

    /**
     * Параметры для вкладки описания продукта
     * @var string
     */
    protected $endForDescription = '?track=tabs';

    public function __construct(Page $page)
    {
        $this->page = $page;
        $this->link = $page->link;
        $this->domen = $this->getDomen($this->link->link);
    }

    /**
     * Обработчик парсинга ссылок со страниц категорий
     * @return $this
     */
    public function parsingLinks()
    {
        $dom = \phpQuery::newDocument($this->page->content);
//        $this->outputNextLink = $this->searchingNextButton($dom);
        $this->outputLinks = $this->searchingCardItemLinks($dom);
        \phpQuery::unloadDocuments();
        return $this;
    }

    /**
     * Обработчик парсинга контента страницы продукта
     * @return $this
     */
    public function parsingProductData()
    {
        $dom = \phpQuery::newDocument($this->page->content);
        $this->product = $this->searchingProductContent($dom);
        \phpQuery::unloadDocuments();
        return $this;
    }

    public function parsingProductImagesLinks()
    {
        $dom = \phpQuery::newDocument($this->page->content);
        $this->images = $this->searchingProductImages($dom);
        $this->productMarketId = $this->getProductId();
        \phpQuery::unloadDocuments();
        return $this;
    }

//    /**
//     * Метод поиска ссылки на следующую страницу категории
//     * @param $dom
//     * @return null|array
//     */
//    protected function searchingNextButton($dom)
//    {
//        $link = $dom->find(".n-pager__button-next")->attr('href') ?? null;
//        return $link
//            ? [
//                'link' => $this->domen . $link,
//                'order_id' => $this->link,
//            ]
//            : null;
//    }

    /**
     * Метод поиска ссылок на товары со страницы категории
     * @param $dom
     * @return array
     */
    protected function searchingCardItemLinks($dom) :array
    {
        $arr = [];
        $d = $dom->find('.i-bem.b-zone.b-spy-visible.b-spy-events h3 > a');

        $nextCategoryLink = $dom->find(".n-pager__button-next")->attr('href') ?? null;
        $now = now();
        if($nextCategoryLink){
            array_push($arr,
            [
                'link' => $this->domen . $nextCategoryLink,
                'order_id' => $this->link->order_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach ($d as $item) {
            $i = pq($item)->attr('href');
            $links = $this->buildProductLink($i);
            if(count($links) > 0){
                array_push($arr, [
                    'link' => $links[0],
                    'order_id' => $this->link->order_id,
                    'type' => Link::$PRODUCT_TYPE_SPECIFICATION,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                array_push($arr, [
                    'link' => $links[1],
                    'order_id' => $this->link->order_id,
                    'type' => Link::$PRODUCT_TYPE_DESCRIPTION,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        return $arr;
    }

    /**
     * Получение домена по ссылке на текущую страницу
     * @param $url
     * @return null
     */
    public function getDomen($url)
    {
        if(preg_match('/^https?:\/\/[^\/]*(\/|$)/m', $url, $matches)){
            $res = $matches[0];
        }else{
            $res = null;
        }
        return $res;
    }

    /**
     * Метод обработки ссылок на продукт (и валидация от редиректа на товары других категорий)
     * - возвращается массив [ссылка на характеристики, ссылка на описание]
     * @param $url
     * @return array
     */
    protected function buildProductLink($url) :array
    {
        $i = preg_replace('/\?.*/', '', $url);
        $redirect = preg_match('/\/redir\//', $i);
        return ($i && !$redirect)
            ? [$this->domen . $i . $this->endForSpecification, $this->domen . $i . $this->endForDescription ]
            : [];
    }

    /**
     * Метод обновления состояния обработанной страницы до "завершен"
     * @return $this
     */
    public function setCurrentPageIsDone()
    {
        $this->page->update(['is_done' => 1]);
        return $this;
    }

    /**
     * Обработчик контента страницы продукта
     * @param $dom
     * @return array
     */
    protected function searchingProductContent($dom) :array
    {
        $now = now();
        $content = [];


        $a = $dom->find('._27nuSZ19h7');
        $href = $a->attr('href');
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
            'link' => $this->link->link,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        return $arr;
    }

    /**
     * @return null|string|string[]
     */
    public function getProductId()
    {
        $link = $this->page->link()->first()->link ?? null;

        return preg_replace(['/.*\/.*\//', '/(\?|\/).*/'], '', $link);
    }

    protected function searchingProductImages($dom) :array
    {
        $images = [];
        $divs = $dom->find('#ProductImageGallery > div');
        foreach ($divs as $div) {
            $i = pq($div);
            $url = $i->contents()->eq(0)->attr('content');
            if($url){
                array_push($images, [
                    'link' => $url,
                    'name' => md5($url),
                ]);
            }
        }
        return $images;
    }
}