<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateLinksFromCategory;
use App\Jobs\GenerateLinksFromCategoryPlitka;
use App\Jobs\GetImage;
use App\Jobs\GetPage;
use App\Jobs\ParsingProductContent;
use App\Jobs\ParsingProductImagesLinks;
use App\Models\Image;
use App\Models\Link;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\Proxy;
use App\Models\Setting;
use App\Services\CurlService;
use App\Services\OrderProcessingService;
use App\Services\OrderProcessingServicePlitka;
use App\Services\ParserService;
use App\Services\ParserServicePlitka;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MainPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

//        Page::where('type', 1)->update(['is_done' => 0]);
//
//        dd(45);
        (new OrderProcessingServicePlitka())->processing();
        dd(4);
//            $a = (new ParserServicePlitka(Page::find(57)))->parsingProductData();
//            dd($a);
//        $productLinks = Link::productLinksReadyToProcess()->limit(200)->get();
//        $chunks = $productLinks->chunk(10);
//        foreach ($chunks as $chunk) {
//            GetPage::dispatch($chunk->values())->onQueue('categoryPage');
//        }
        return view('welcome');
    }

    public function createFile()
    {
        $products = Product::select('id', 'market_id', 'title', 'content', 'link')->with('images', 'links')->get();
        $fp = fopen('file.csv', 'w');
        fputcsv($fp, ['market_id', 'title', 'content', 'link', 'images'], '|');
        foreach ($products as $i) {
            $i->images->map(function ($i) {
                return $i->name = $i->name . '.jpg';
            });
            $images = implode(',', $i->images->pluck('name')->toArray());
            $arrr = [];
            foreach (json_decode($i->content) as $key => $item) {
                array_push($arrr, preg_replace('/\\n/', '', $key) . '---' . $item);
            }
            $arr = [
                $i->market_id,
                $i->title,
                implode(',', $arrr),
                $i->links->link,
                $images
            ];
            fputcsv($fp, $arr, '|');
        }

        fclose($fp);
        dd(1);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $arr = [
            'SOCKS4' => 'CURLPROXY_SOCKS4',
            'SOCKS5' => 'CURLPROXY_SOCKS5',
        ];
        $data = ($request->only('value'))['value'];

        foreach ($data as &$item) {
            $a = explode(',', $item['type']);
            $b = array_pop($a);
            $item['type'] = $arr[$b] ?? 'CURLPROXY_SOCKS5';
            Proxy::firstOrCreate(
                ['proxy' => $item['proxy']],
                ['type' => $item['type'], 'created_at' => now()->format('Y-m-d H:i:s'), 'updated_at' => now()->format('Y-m-d H:i:s')]
            );
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
