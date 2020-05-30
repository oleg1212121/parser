<?php

namespace App\Console\Commands;

use App\Jobs\GenerateLinksFromCategory;
use App\Jobs\GetPage;
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

        $firstJob = \DB::table('jobs')->first();

        if(!$firstJob) {
            $categoryLinks = Link::categoryLinksReadyToProcess()->limit(400)->get();
            if (count($categoryLinks) > 0) {
                $chunks = $categoryLinks->chunk(4);
                foreach ($chunks as $chunk) {
                    GetPage::dispatch($chunk->values());
                }
            } else {
                $productLinks = Link::productLinksReadyToProcess()->limit(400)->get();
                if (count($productLinks) > 0) {
                    $chunks = $productLinks->chunk(4);
                    foreach ($chunks as $chunk) {
                        GetPage::dispatch($chunk->values());
                    }
                } else {
                    $categoryPages = Page::where('type', 0)->where('is_done', 0)->limit(20)->get();
                    if (count($categoryPages) > 0) {
                        foreach ($categoryPages as $page) {
                            GenerateLinksFromCategory::dispatch($page);
                        }
                    } else {
                        $productPages = Page::where('type', 1)->where('is_done', 0)->limit(20)->get();
                        if (count($productPages) > 0) {
                            $this->info('PAGES PRODUCT');
                            foreach ($productPages as $page) {
//                                GenerateLinksFromCategory::dispatch($chunk->values());
//                                https://market.yandex.by/catalog--mobilnye-telefony-v-minske/54726/list?hid=91491&local-offers-first=0&onstock=1&lr=0&rtr=35&page=2
//                                https://market.yandex.by/catalog--mobilnye-telefony-v-minske/54726/list?hid=91491&local-offers-first=0&onstock=1&lr=0&rtr=35&page=2
                            }
                        }
                    }
                }
            }
        }
    }
}
