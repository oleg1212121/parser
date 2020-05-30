<?php

namespace App\Jobs;

use App\Models\Link;
use App\Models\Proxy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class GetPage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    /**
     * Набор ссылок для запросов
     * @var array|Collection
     */
    protected $links = [];

    /**
     * Массив успешно полученных ответов
     * @var array
     */
    protected $pages = [];

    /**
     * Массив используемых прокси адресов
     * @var array
     */
    protected $proxies = [];

    /**
     * Массив user-agent
     * @var array
     */
    protected $agents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.118 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 Safari/537.36\x09Chrome Generic',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36 OPR/67.0.3575.97',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.1.195 Yowser/2.5 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 YaBrowser/20.3.2.238 Yowser/2.5 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36 OPR/67.0.3575.137',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.122 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.112',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.118',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36 OPR/68.0.3618.63 (Edition Campaign 21)',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.132 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 Edg/81.0.416.77',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36,gzip(gfe)',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.104 (Edition Campaign 34)',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.125',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.125 (Edition Campaign 34)',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.125 (Edition utorrent)',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.125 (Edition Yx)',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.125 (Edition Yx 02)',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.125 (Edition Yx 03)',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.125 (Edition Yx 05)',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.129',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.129 (Edition Yx GX)',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.142',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.142 (Edition Campaign 34)',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.142 (Edition Yx GX)',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.201 Yowser/2.5 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.201 Yowser/2.5 Yptp/1.21 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.2.201 Yowser/2.5 Yptp/1.23 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 YaBrowser/20.4.3.255 Yowser/2.5 Safari/537.36',
        ];


    /**
     * Create a new job instance.
     *
     * @param array|Collection $links
     * @return void
     */
    public function __construct( $links = [] )
    {
        $this->links = $links;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->proxies = Proxy::freeProxy()->limit(count($this->links))->get();
        $linksCount = count($this->links);
        $proxiesCount = count($this->proxies);
        if( $linksCount > 0 || $proxiesCount > 0 || $linksCount <= $proxiesCount ){
            $this->sendingRequests();
            $this->processingResponse();
        }else{
            $this->fail(new \Exception('Not enough proxies.'));
        }
    }

    /**
     * - Сохранение валидных страниц в базу
     * - Инкремент счетчика ошибок прокси
     * - Присвоеное статуса "выполнено" для успешно обработанных
     *
     * @return void
     */
    protected function processingResponse()
    {
        \DB::transaction(function (){
            if (count($this->pages) > 0) {
                \DB::table('pages')->insert($this->pages);
            }
            if (count($this->proxies) > 0) {
                Proxy::whereIn('id', $this->proxies->pluck('id'))->increment('fails');
            }
            if (count($this->links) > 0) {
                Link::whereIn('id', $this->links->pluck('id'))->update(['is_done' => 1]);
            }
        });
    }

    /**
     * - Создание набора дескрипторов для запросов
     * - Запись ответов в массив pages
     * - Проверка контента на валидность
     *
     * @return void
     */
    protected function sendingRequests()
    {
        $curly = [];
        $mh = curl_multi_init();

        foreach ($this->links as $id => $url) {
            $curly[$id] = curl_init();
            curl_setopt($curly[$id], CURLOPT_PROXY, $this->proxies[$id]->proxy); // ip прокси (имя:пароль@124.11.22.32:1028 / 124.65.12.55:8080)
            curl_setopt($curly[$id], CURLOPT_PROXYTYPE, constant($this->proxies[$id]->type ?? 'CURLPROXY_HTTP')); // type прокси socks5/4 , http , https
            curl_setopt($curly[$id], CURLOPT_URL, $url->link);
            curl_setopt($curly[$id], CURLOPT_HEADER, false);
            curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curly[$id], CURLOPT_TIMEOUT, 40);
            curl_setopt($curly[$id], CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curly[$id], CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curly[$id], CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curly[$id], CURLOPT_USERAGENT, $this->agents[array_rand($this->agents, 1)]);
            curl_setopt($curly[$id], CURLOPT_CONNECTTIMEOUT, 6); // время установки соединения
            curl_setopt($curly[$id], CURLOPT_NOBODY, false); // не показывать тело ответа
            curl_multi_add_handle($mh, $curly[$id]);
        }

        $running = null;
        do {
            $status = curl_multi_exec($mh, $running);
        } while ($running > 0 && $status == CURLM_CALL_MULTI_PERFORM);

        while ($running && $status == CURLM_OK) {
            if (curl_multi_select($mh) == -1) {
                usleep(100);
                continue;
            }
            do {
                $status = curl_multi_exec($mh, $running);
            } while ($mh == CURLM_CALL_MULTI_PERFORM);
        }
        $now = now();
        foreach ($curly as $id => $c) {
            $content = curl_multi_getcontent($c);

            array_push($this->pages, [
                'content' => $content,
                'type' => $this->links[$id]->type,
                'link_id' => $this->links[$id]->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            curl_multi_remove_handle($mh, $c);
        }
        curl_multi_close($mh);

        foreach ($this->pages as $key => $page) {
            if (!$page['content'] || strlen($page['content']) < 10000) {
                unset($this->links[$key]);
                unset($this->pages[$key]);
            }else{
                unset($this->proxies[$key]);
            }
        }
    }

}
