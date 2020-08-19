<?php
/**
 * Created by PhpStorm.
 * User: aleksandr
 * Date: 26.06.20
 * Time: 18:59
 */

namespace App\Services;


use App\Models\Link;
use App\Models\Page;
use PHPHtmlParser\Dom;

class ParserServicePlitka
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
        $dom = new Dom();
        $dom->load($this->page->content, ['cleanupInput' => false]);
        $this->outputLinks = $this->searchingCardItemLinks($dom);
        return $this;
    }

    /**
     * Обработчик парсинга контента страницы продукта
     * @return $this
     */
    public function parsingProductData()
    {
        $dom = new Dom();
        $dom->load($this->page->content, ['cleanupInput' => false]);
        $this->product = $this->searchingProductContent($dom);
        $this->images = $this->searchingProductImages($dom);
        $this->productMarketId = $this->getProductId();
        return $this;
    }

    public function parsingProductImagesLinks()
    {
        $dom = new Dom();
        $dom->load($this->page->content, ['cleanupInput' => false]);
        $this->images = $this->searchingProductImages($dom);
        $this->productMarketId = $this->getProductId();
        return $this;
    }


    /**
     * Метод поиска ссылок на товары со страницы категории
     * @param $dom
     * @return array
     */
    protected function searchingCardItemLinks($dom): array
    {
        $arr = [];
        $items = $dom->find('.product-card .product-card-link');
        $now = now()->format('Y-m-d H:i:s');

        foreach ($items as $item) {
            $href = $item->getAttribute('href');

            $links = $this->buildProductLink($href);

            if (count($links) > 0) {
                array_push($arr, [
                    'link' => $links[0],
                    'order_id' => $this->link->order_id,
                    'type' => Link::$PRODUCT_TYPE_SPECIFICATION,
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
        if (preg_match('/^https?:\/\/[^\/]*(\/|$)/m', $url, $matches)) {
            $res = $matches[0];
        } else {
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
    protected function buildProductLink($url): array
    {
        $i = preg_replace(['/^\//', '/\?.*/'], '', $url);
//        $redirect = preg_match('/\/redir\//', $i);
        return ($i)
            ? [$this->domen . $i]
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
    protected function searchingProductContent($dom): array
    {
        $now = now()->format('Y-m-d H:i:s');
        $content = [];
        $product = [];
        $id = null;
        $title = null;

//        $a = $dom->find('._3nG1s9PJnI', 0);


        $title = $dom->find(".header-line h1", 0);
        $title = trim($title ? $title->text : null);
        $spec = $dom->find("#pane-attrs .attr-row");

        foreach ($spec as $item) {
            $attr = $item->find('.attr-name', 0);

            $value1 = $item->find('.attr-value', 0);
            $value2 = $item->find('.attr-value span', 0);
            $value3 = $item->find('.attr-value span span', 0);

            if ($attr && ($value1 || $value2 || $value3)) {
                $k = trim($attr->text);
                $v = trim($value3 ? $value3->text : ($value2 ? $value2->text : $value1->text));
                if ($k && $v) {
                    $content[$k] = $v;
                }
            }
        }

        if (count($content) > 0 && $title) {
            $product = [
                'market_id' => md5($this->link->link),
                'title' => $title,
                'content' => json_encode($content),
                'link' => $this->link->link,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }


        return $product;
    }

    /**
     * @return null|string|string[]
     */
    public function getProductId()
    {
        return md5($this->link->link ?? null);
    }

    protected function searchingProductImages($dom): array
    {
        $images = [];
        $links = $dom->find('#product-images-container > link');
        foreach ($links as $link) {
            $image = $link->getAttribute('href');
                array_push($images, [
                    'link' => $image,
                    'name' => md5($image),
                ]);

        }
        return $images;
    }
}