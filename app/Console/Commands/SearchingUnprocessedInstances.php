<?php

namespace App\Console\Commands;

use App\Jobs\GenerateLinksFromCategory;
use App\Jobs\GetImage;
use App\Jobs\GetPage;
use App\Jobs\ParsingProductContent;
use App\Jobs\ParsingProductImagesLinks;
use App\Models\Image;
use App\Models\Link;
use App\Models\Page;
use Illuminate\Console\Command;

class SearchingUnprocessedInstances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'searchingUnprocessed:instances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Searching unprocessed links or pages and generating jobs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $chunkSize = 10;


        $first = \DB::table('jobs')->where('queue', 'categoryLink')->first();
        $second = \DB::table('jobs')->where('queue', 'categoryPage')->first();
        $third = \DB::table('jobs')->where('queue', 'productLink')->first();
        $fourth = \DB::table('jobs')->where('queue', 'productPage')->first();
        $fifth = \DB::table('jobs')->where('queue', 'image')->first();

        if (!$first) {
            $categoryLinks = Link::categoryLinksReadyToProcess()->limit(200)->get();
            $chunks = $categoryLinks->chunk($chunkSize);
            $this->info(count($chunks));
            foreach ($chunks as $chunk) {
                GetPage::dispatch($chunk->values())->onQueue('categoryLink');
            }
        }

        if (!$second) {
            $productLinks = Link::productLinksReadyToProcess()->limit(200)->get();
            $chunks = $productLinks->chunk($chunkSize);
            $this->info(count($chunks));
            foreach ($chunks as $chunk) {
                GetPage::dispatch($chunk->values())->onQueue('categoryPage');
            }
        }
        if (!$third) {
            $categoryPages = Page::categoryPagesReadyToProcess()->limit(30)->get();
            foreach ($categoryPages as $page) {
                GenerateLinksFromCategory::dispatch($page)->onQueue('productLink');
            }
            $this->info(count($categoryPages));
        }
        $productPages = [];
        if (!$fourth) {
            $productPages = Page::productPagesReadyToProcess()->limit(30)->get();
            $this->info(count($productPages));
            if (count($productPages) > 0) {
                foreach ($productPages as $page) {
                    ParsingProductContent::dispatch($page)->onQueue('productPage');
                }
            } else {
                $productPages = Page::notDone()->where('type', Page::$PRODUCT_TYPE_DESCRIPTION)->limit(50)->get();

                if (!$fifth) {
                    foreach ($productPages as $page) {
                        ParsingProductImagesLinks::dispatch($page)->onQueue('image');
                    }
                }
            }
        }
        if (!$first && !$second && !$third && !$fourth && !$fifth && count($productPages) == 0) {
            $images = Image::where('is_done', 0)->limit(60)->get();
            $chunks = $images->chunk($chunkSize);
            foreach ($chunks as $chunk) {
                GetImage::dispatch($chunk->values())->onQueue('default');
            }
        }

    }
}
