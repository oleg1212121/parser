<?php
/**
 * Created by PhpStorm.
 * User: Dimsa
 * Date: 08.06.2020
 * Time: 18:29
 */

namespace App\Services;


use App\Jobs\GenerateLinksFromCategory;
use App\Jobs\GetImage;
use App\Jobs\GetPage;
use App\Jobs\ParsingProductContent;
use App\Jobs\ParsingProductImagesLinks;
use App\Models\Image;
use App\Models\Link;
use App\Models\Order;
use App\Models\Page;

class OrderProcessingService
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
        $chunkSize = 10;


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
            $categoryLinks = Link::categoryLinksReadyToProcess()->limit(200)->get();
            $chunks = $categoryLinks->chunk($chunkSize);
            foreach ($chunks as $chunk) {
                GetPage::dispatch($chunk->values())->onQueue('categoryLink');
            }
        }

        if (!$second) {
            $productLinks = Link::productLinksReadyToProcess()->limit(200)->get();
            $chunks = $productLinks->chunk($chunkSize);
            foreach ($chunks as $chunk) {
                GetPage::dispatch($chunk->values())->onQueue('categoryPage');
            }
        }
        if (!$third) {
            $categoryPages = Page::categoryPagesReadyToProcess()->with('link')->limit(30)->get();
            foreach ($categoryPages as $page) {
                GenerateLinksFromCategory::dispatch($page)->onQueue('productLink');
            }
        }
        if (!$fourth) {
            $productPages = Page::productPagesReadyToProcess()->with('link')->limit(30)->get();
            if (count($productPages) > 0) {
                foreach ($productPages as $page) {
                    ParsingProductContent::dispatch($page, $this->order)->onQueue('productPage');
                }
            } else {
                if (count($categoryLinks) == 0 && count($productLinks) == 0 && count($categoryPages) == 0) {
                    $productPages = Page::productDescriptionsReadyToProcess()->with('link')->limit(50)->get();
                    foreach ($productPages as $page) {
                        ParsingProductImagesLinks::dispatch($page)->onQueue('productPage');
                    }
                }
            }
        }
        if (!$fifth && count($categoryLinks) == 0 && count($productLinks) == 0 && count($categoryPages) == 0 && count($productPages) == 0) {
           if(in_array('images', $this->settings)){
               $images = Image::notDone()->limit(60)->get();
               $chunks = $images->chunk($chunkSize);
               foreach ($chunks as $chunk) {
                   GetImage::dispatch($chunk->values())->onQueue('image');
               }
               if(count($images) == 0){
                   $this->order->update(['is_done' => 1]);
               }
           }else{
               $this->order->update(['is_done' => 1]);
           }
        }
    }
}