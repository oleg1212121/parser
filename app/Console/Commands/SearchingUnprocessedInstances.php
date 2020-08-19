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
use App\Services\OrderProcessingService;
use App\Services\OrderProcessingServicePlitka;
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
        (new OrderProcessingServicePlitka())->processing();
//        (new OrderProcessingService())->processing();

    }
}
