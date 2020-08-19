<?php
/**
 * Created by PhpStorm.
 * User: aleksandr
 * Date: 26.06.20
 * Time: 18:02
 */

namespace App\Services;


use App\Jobs\GenerateLinksFromCategory;
use App\Jobs\GenerateLinksFromCategoryPlitka;
use App\Jobs\GetImage;
use App\Jobs\GetPage;
use App\Jobs\ParsingProductContent;
use App\Jobs\ParsingProductContentPlitka;
use App\Jobs\ParsingProductImagesLinks;
use App\Jobs\ParsingProductImagesLinksPlitka;
use App\Models\Image;
use App\Models\Link;
use App\Models\Order;
use App\Models\Page;

class OrderProcessingServicePlitka
{
    protected $order = null;
    protected $settings = [];
    protected $queues = [];

    public function __construct()
    {
        $this->getOrder();
        $this->getSettings();
        $this->checkQueues();
    }

    public function getOrder()
    {
        $this->order = Order::whereNotNull('published_at')->with('settings')->orderBy('priority')->orderBy('created_at')->first();
    }

    protected function getSettings()
    {
        $this->settings = $this->order->settings->pluck('name')->toArray() ?? [];
    }

    protected function checkQueues()
    {
        $this->queues = \DB::table('jobs')->get()->groupBy('queue')->keys()->toArray() ?? [];
    }


    public function processing()
    {
        if ($this->order) {
            $chunkSize = 20;


            $first = in_array('categoryLink', $this->queues);
            $second = in_array('categoryPage', $this->queues);
            $third = in_array('productLink', $this->queues);
            $fourth = in_array('productPage', $this->queues);
            $fifth = in_array('image', $this->queues);

            $categoryLinks = collect(['test']);
            $productLinks = collect(['test']);
            $categoryPages = collect(['test']);
            $productPages = collect(['test']);

            if (!$first) {
                $categoryLinks = Link::categoryLinksReadyToProcess()->forOrder($this->order->id)->limit(200)->get();
                $chunks = $categoryLinks->chunk($chunkSize);
                foreach ($chunks as $chunk) {
                    GetPage::dispatch($chunk->values())->onQueue('categoryLink');
                }
            }

            if (!$second) {
                $productLinks = Link::productLinksReadyToProcess()->forOrder($this->order->id)->limit(200)->get();
                $chunks = $productLinks->chunk($chunkSize);
                foreach ($chunks as $chunk) {
                    GetPage::dispatch($chunk->values())->onQueue('categoryPage');
                }
            }
            if (!$third) {
                $categoryPages = Page::categoryPagesReadyToProcess()->forOrder($this->order->id)->with('link')->limit(30)->get();
                foreach ($categoryPages as $page) {
                    GenerateLinksFromCategoryPlitka::dispatch($page)->onQueue('productLink');
                }
            }
            if (!$fourth) {
                $productPages = Page::productPagesReadyToProcess()->forOrder($this->order->id)->with('link')->limit(30)->get();

                foreach ($productPages as $page) {
                    ParsingProductContentPlitka::dispatch($page, $this->order)->onQueue('productPage');
                }

            }

            if (!$fifth && count($categoryLinks) == 0 && count($productLinks) == 0 && count($categoryPages) == 0 && count($productPages) == 0) {
                if (in_array('images', $this->settings)) {
                    $images = Image::notDone()->whereHas('products', function ($query) {
                        $query->whereHas('orders', function ($q) {
                           $q->where('order_id', $this->order->id);
                        });
                    })->limit(200)->get();
                    $chunks = $images->chunk($chunkSize);
                    foreach ($chunks as $chunk) {
                        GetImage::dispatch($chunk->values())->onQueue('image');
                    }
                    if (count($images) == 0) {
                        $this->order->update(['is_done' => 1]);
                    }
                } else {
                    $this->order->update(['is_done' => 1]);
                }
            }
        }
    }
}