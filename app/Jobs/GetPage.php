<?php

namespace App\Jobs;

use App\Models\Link;
use App\Models\Page;
use App\Models\Proxy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetPage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 240;
    protected $url = '';
    protected $proxy = '';
    protected $types = [
        'CURLPROXY_SOCKS5' => CURLPROXY_SOCKS5,
        'CURLPROXY_SOCKS4' => CURLPROXY_SOCKS4,
        'CURLPROXY_HTTP' => CURLPROXY_HTTP,
        'CURLPROXY_HTTPS' => CURLPROXY_HTTPS,
        'CURLPROXY_HTTP_1_0' => CURLPROXY_HTTP_1_0,
        'CURLPROXY_SOCKS4A' => CURLPROXY_SOCKS4A,
    ];


    /**
     * Create a new job instance.
     *
     * @param $url
     * @param $proxy
     * @return void
     */
    public function __construct(Link $url,Proxy $proxy)
    {
        $this->url = $url;
        $this->proxy = $proxy;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $headers = array
        (
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
            'Accept-Encoding: gzip, deflate, br',
            'Cache-Control: max-age=0',
            'Connection: keep-alive'
        );
        $user_cookie_file = 'cookies.txt';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url->link); // url запроса
        curl_setopt($ch, CURLOPT_PROXY, $this->proxy->proxy); // ip прокси (имя:пароль@124.11.22.32:1028 / 124.65.12.55:8080)
        curl_setopt($ch, CURLOPT_PROXYTYPE, $this->types[$this->proxy->type]); // type прокси socks5/4 , http , https

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //ответ строкой
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // следовать за редиректами
        curl_setopt($ch, CURLOPT_USERAGENT,
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36'); // header useragent - от имени какого браузера идет запрос

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // проверка сертификата ?
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // проверка сертификата ?

        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // время ожидания результата
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20); // время установки соединения

        curl_setopt($ch, CURLOPT_HEADER, false); // ПОказывать заголовки
        curl_setopt($ch, CURLOPT_NOBODY, false); // не показывать тело ответа
        curl_setopt($ch, CURLOPT_COOKIEJAR, $user_cookie_file); // cookie
        curl_setopt($ch, CURLOPT_COOKIEFILE, $user_cookie_file); // cookie
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // headers

        $html = curl_exec($ch);
        curl_close($ch);


        Page::create([
            'content' => $html,
            'link_id' => $this->url->id ?? null
        ]);
    }
}
