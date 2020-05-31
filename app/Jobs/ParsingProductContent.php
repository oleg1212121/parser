<?php

namespace App\Jobs;

use App\Models\Page;
use App\Models\Product;
use App\Services\ParserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParsingProductContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $page = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Page $page = null)
    {
        $this->page = $page;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->page) {
            $product = (new ParserService($this->page))->parsingProduct();
            \DB::transaction(function () use ($product) {
                Product::create($product->product);
                $product->setCurrentPageIsDone();
            });
        }
    }
}
