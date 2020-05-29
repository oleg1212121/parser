<?php

namespace App\Console\Commands;

use App\Jobs\GetPage;
use App\Models\Link;
use Illuminate\Console\Command;

class GetProductPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getPages:product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get products pages from links';

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
        $chunks = Link::where('is_done', 0)->whereType(1)->limit(400)->get()->chunk(4);
        foreach ($chunks as $chunk) {
            GetPage::dispatch($chunk->values());
        }
    }
}
