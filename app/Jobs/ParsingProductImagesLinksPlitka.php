<?php

namespace App\Jobs;

use App\Models\Image;
use App\Models\Page;
use App\Models\Product;
use App\Services\ParserServicePlitka;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParsingProductImagesLinksPlitka implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $page;
    public $timeout = 120;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Page $page)
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
        if($this->page){
            $productData = (new ParserServicePlitka($this->page))->parsingProductImagesLinks();
            $images = $productData->images;
            $product = Product::where('market_id', $productData->productMarketId)->first();

            \DB::transaction(function () use ($images, $product, $productData){
                $ids = [];
                foreach ($images as $item) {
                    $image = Image::firstOrCreate([
                        'name' => $item['name']
                    ], [
                        'link' => $item['link'],
                    ]);
                    array_push($ids, $image->id);
                }
                if($product){
                    $product->images()->sync($ids);
                    $productData->setCurrentPageIsDone();
                }
            });
        }
    }
}
