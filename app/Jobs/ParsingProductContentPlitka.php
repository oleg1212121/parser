<?php

namespace App\Jobs;

use App\Models\Image;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Services\ParserServicePlitka;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParsingProductContentPlitka implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $page = null;
    protected $order = null;
    public $timeout = 120;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Page $page = null, Order $order = null)
    {
        $this->page = $page;
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->page && $this->order) {
            $productData = (new ParserServicePlitka($this->page))->parsingProductData();
            $images = $productData->images;

            \DB::transaction(function () use ($productData, $images) {
                $product = Product::firstOrCreate(
                    [
                        'market_id' => $productData->product['market_id']
                    ],
                    [
                        'title' => $productData->product['title'],
                        'content' => $productData->product['content'],
                        'link' => $productData->product['link'],
                    ]
                );

                $this->order->products()->syncWithoutDetaching($product->id);


                if ($product) {
                    $ids = [];
                    foreach ($images as $item) {
                        $image = Image::firstOrCreate([
                            'name' => $item['name']
                        ], [
                            'link' => $item['link'],
                        ]);
                        array_push($ids, $image->id);
                    }
                    $product->images()->sync($ids);
                    $productData->setCurrentPageIsDone();
                }

//                $productData->setCurrentPageIsDone();
            });


//            $product = Product::where('market_id', $productData->productMarketId)->first();

//            \DB::transaction(function () use ($images, $product, $productData){

//            });
        }
    }
}
