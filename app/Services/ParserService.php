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
    /**
     * Массив ссылок на продукты (парсится со страниц категорий)
     * @var array
     */
    public $outputLinks = [];

    /**
     * Ссылка на следующую страницу категории (по пагинации)
     * @var null
     */
    public $outputNextLink = null;

    /**
     * Массив данных для создания продукта в БД (парсится со страниц продукта)
     * @var array
     */
    public $product = [];

    /**
     * Страница для парсинга
     * @var Page
     */
    protected $page;

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
    }

    /**
     * Обработчик парсинга ссылок со страниц категорий
     * @return $this
     */
    public function parsingLinks()
    {
        $dom = \phpQuery::newDocument($this->page->content);
        $this->outputNextLink = $this->searchingNextButton($dom);
        $this->outputLinks = $this->searchingCardItemLinks($dom);
        \phpQuery::unloadDocuments();
        return $this;
    }

    /**
     * Обработчик парсинга контента страницы продукта
     * @return $this
     */
    public function parsingProduct()
    {
        $dom = \phpQuery::newDocument($this->page->content);
        $this->product = $this->searchingProductContent($dom);
        \phpQuery::unloadDocuments();
        return $this;
    }

    /**
     * Метод поиска ссылки на следующую страницу категории
     * @param $dom
     * @return null|string
     */
    protected function searchingNextButton($dom)
    {
        $link = $dom->find(".n-pager__button-next")->attr('href') ?? null;
        return $link ? $this->domen . $link : null;
    }

    /**
     * Метод поиска ссылок на товары со страницы категории
     * @param $dom
     * @return array
     */
    protected function searchingCardItemLinks($dom) :array
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

    /**
     * Метод обработки ссылок на продукт (и валидация от редиректа на товары других категорий)
     * @param $url
     * @return string
     */
    protected function buildProductLink($url) :string
    {
        $i = preg_replace('/\?.*/', '', $url);
        $redirect = preg_match('/\/redir\//', $i);
        return ($i && !$redirect) ? $this->domen . $i . $this->endForSpecification : '';
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