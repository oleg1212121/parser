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
    use InteractsWithQueue;

    public $tries = 3;

    protected $url;
    protected $proxy ;


    /**
     * Create a new job instance.
     *
     * @param $url
     * @return void
     */
    public function __construct(Link $url)
    {
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $proxy = Proxy::getFreeProxy();
        $this->proxy = $proxy;

        if($this->proxy) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url->link); // url запроса
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy->proxy); // ip прокси (имя:пароль@124.11.22.32:1028 / 124.65.12.55:8080)
            curl_setopt($ch, CURLOPT_PROXYTYPE, constant($this->proxy->type ?? 'CURLPROXY_HTTP')); // type прокси socks5/4 , http , https

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


            $html = curl_exec($ch);
            curl_close($ch);

            // если длина контента меньше 10к - это страница с капчей
            if (!$html || strlen($html) < 10000) {
                $this->updateFailedJob();
                $this->newRelease();
            } else {
                \DB::transaction(function () use ($html) {
                    $this->url->update([
                        'is_done' => 1
                    ]);
                    Page::create([
                        'content' => $html,
                        'link_id' => $this->url->id ?? null
                    ]);
                });
            }
        }else{
            $this->newRelease();
        }

    }

    /**
     * The job failed to process.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        $this->updateFailedJob();
    }

    /**
     * В зависимости от текущей попытки увеличивает delay следующей попытки
     * в конце - fail
     */
    protected function newRelease()
    {
        if($this->attempts() < 2){
            $period = 10;
        }else{
            $period = 30;
        }
        $this->release($period);
    }

    /**
     * Увеличить счетчик ошибок прокси
     */
    protected function updateFailedJob()
    {
        if($this->proxy){
            $this->proxy->update([
                'fails' => $this->proxy->fails + 1,
            ]);
        }
    }
}
