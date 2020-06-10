<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateLinksFromCategory;
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
use App\Services\ParserService;
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
        return view('welcome');
//dd(count(collect([])));
//        dd(Page::productPagesReadyToProcess()->with('link')->limit(30)->get());
//        \DB::transaction(function () {
//        $o = Order::create([
//            'name' => 'pepe',
//            'description' => 'pepepepepepepe',
//            'published_at' => now()
//        ]);
//        $s = Setting::create([
//            'name' => 'images',
//            'slug' => 'image create'
//        ]);
//        $o->settings()->syncWithoutDetaching($s->id);
//        $link = 'https://market.yandex.by/catalog--gazonokosilki-v-minske/71963/list?hid=15646095&utm_source=&utm_campaign=&utm_content=&glfilter=4863061%3A1&promo-type=&local-offers-first=0&onstock=1&fesh=260888';
//        $l = Link::create([
//            'link' => $link,
//            'order_id' => $o->id
//        ]);
//    });

//        $parser = (new ParserService($this->page))->parsingLinks()

(new OrderProcessingService())->processing();
dd(322);

       $agent =  'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36';
//        $link = 'https://www.tut.by/';
        $link = [
            'https://market.yandex.by/product--pogruzhnoi-blender-philips-hr2653-90-viva-collection/415599006?show-uid=15915653802708696926716001&nid=54931&context=search',
            'https://market.yandex.by/product--statsionarnyi-blender-philips-hr3752-00-avance-collection/73786422?show-uid=15915653802708696926716002&nid=54931&context=search',
            'https://market.yandex.by/product--statsionarnyi-blender-philips-hr3752-00-avance-collection/73786422/spec?track=tabs',
            'https://market.yandex.by/product--pogruzhnoi-blender-philips-hr2653-90-viva-collection/415599006/spec?track=tabs',
            'https://market.yandex.by/product--statsionarnyi-blender-philips-hr2600/515114074?show-uid=15915653802708696926716003&nid=54931&context=search',
            'https://market.yandex.by/product--statsionarnyi-blender-philips-hr2600/515114074/spec?track=tabs',
];
        $proxy = Proxy::freeProxy()->find(4);



        $html = $this->quer($link[1],$proxy, $agent);

//        dd($html);
        $answer = urlencode('НАВИГ Y-YES.');
        $retpath = urlencode('https://market.yandex.by/product--planshet-samsung-galaxy-tab-a-10-1-sm-t515-32gb/432101256?_ae0599701345c4c7344b6c322e504241');
        $key = urlencode('00A2BSZkZMEe8wyBcLgsd2yfwAiVzGNc_0/1591563714/ca5fcefa21e427f1adcad7a055e94a5c_1d14072d17a47373f20986ccbd6749a1');

        $post = 'https://market.yandex.by/checkcaptcha?'."key=$key&retpath=$retpath&rep=$answer";

//        $html1 = $this->capcha($link,$proxy,$agent,$post);
//
//        $dom = \phpQuery::newDocument($html1);
//        $v = $dom->find('.form__key')->attr('value');
//        \phpQuery::unloadDocuments();
//        $answer = urlencode('TERMIT plural');
//        $key = urlencode($v);
//        $post = 'https://market.yandex.by/checkcaptcha?'."key=$key&retpath=$retpath&rep=$answer";
//
//        $html = $this->capcha($link,$proxy,$agent,$post);
//        $isWinCharset = mb_check_encoding($html, "windows-1251");
//        if ($isWinCharset) {
//            $html = iconv("windows-1251", "UTF-8", $html);
//        }

//dd($html);
        return view('welcome', compact('html'));
    }

    public function quer($link, $proxy, $agent)
    {
        $headers = array
        (
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Accept-Language: ru-RU,ru-BY;q=0.9,ru;q=0.8,en-US;q=0.7,en;q=0.6',
            'Content-Type: text/html; charset=utf-8',
            'Cache-Control: no-cache',
            'Connection: keep-alive',
            'Referer: https://www.google.com/',
        );
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);


        curl_setopt($ch, CURLOPT_PROXY, $proxy->proxy); // ip прокси (имя:пароль@124.11.22.32:1028 / 124.65.12.55:8080)
        curl_setopt($ch, CURLOPT_PROXYTYPE, constant($proxy->type ?? 'CURLPROXY_HTTP')); // type прокси socks5/4 , http , https
        curl_setopt($ch, CURLOPT_URL, $link);
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_COOKIEJAR, storage_path().'/app/cookies/'.($proxy->id).'.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, storage_path().'/app/cookies/'.($proxy->id).'.txt');

        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6); // время установки соединения
//        curl_setopt($ch, CURLOPT_NOBODY, false); // не показывать тело ответа
        $html = curl_exec($ch);
//        $inf = curl_getinfo($ch);
//        dd($inf);
//        00APdD3MZnStsjKZy1otDQiFVjkFJo7w    _0/1591539429/1ffc2fa225646ef2f65eb57bb92e7b0b _2931c11fe2bb42c2c7b79187aeb2d43c
//        _ae0599701345c4c7344b6c322e504241&t =0/1591539429/1ffc2fa225646ef2f65eb57bb92e7b0b &s=42368fcc9cfccc14bde9503d77056efa
//        dd($html);
        curl_close($ch);
        return  $html;
    }
    public function capcha($link, $proxy, $agent, $post)
    {
        $headers = array
        (
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Accept-Language: ru-RU,ru-BY;q=0.9,ru;q=0.8,en-US;q=0.7,en;q=0.6',
//            'Accept-Encoding: gzip, deflate, br',
            'Content-Type: text/html; charset=utf-8',
            'Cache-Control: no-cache',
            'Connection: keep-alive',
//            'Host: market.yandex.by',
//            'Sec-Fetch-Dest: document',
//            'Sec-Fetch-Mode: navigate',
//            'Sec-Fetch-Site: none',
//            'Upgrade-Insecure-Requests: 1',

            'Referer: https://www.google.com/',
        );
        $ch = curl_init();

//        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_PROXY, $proxy->proxy); // ip прокси (имя:пароль@124.11.22.32:1028 / 124.65.12.55:8080)
        curl_setopt($ch, CURLOPT_PROXYTYPE, constant($proxy->type ?? 'CURLPROXY_HTTP')); // type прокси socks5/4 , http , https
        curl_setopt($ch, CURLOPT_URL, $post);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_COOKIEJAR, storage_path().'/app/cookies/'.($proxy->id).'cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, storage_path().'/app/cookies/'.($proxy->id).'cookies.txt');


        curl_setopt($ch, CURLOPT_COOKIESESSION, false);
                curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6); // время установки соединения
//        curl_setopt($ch, CURLOPT_POST, true ); // использовать данные в post
//            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
//        curl_setopt($ch, CURLOPT_NOBODY, false); // не показывать тело ответа



        $html = curl_exec($ch);
        $inf = curl_getinfo($ch);
//        dd($inf);
        curl_close($ch);
//        dd($inf);
        return $html;
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
                ['type' => $item['type'], 'created_at' => now(), 'updated_at' => now()]
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
