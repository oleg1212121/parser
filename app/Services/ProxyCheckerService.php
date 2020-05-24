<?php
/**
 * Created by PhpStorm.
 * User: Dimsa
 * Date: 24.05.2020
 * Time: 16:16
 */

namespace App\Services;

use App\Models\Proxy;

class ProxyCheckerService
{
    public static $PROXY_TYPES = [
        'CURLPROXY_HTTP_1_0' => CURLPROXY_HTTP_1_0,
        'CURLPROXY_HTTP' => CURLPROXY_HTTP,
        'CURLPROXY_HTTPS' => CURLPROXY_HTTPS,
        'CURLPROXY_SOCKS5' => CURLPROXY_SOCKS5,
        'CURLPROXY_SOCKS4' => CURLPROXY_SOCKS4,
        'CURLPROXY_SOCKS4A' => CURLPROXY_SOCKS4A,
    ];

    public static $IP_CHECKER_URL = 'http://httpbin.org/ip';
    public static $WORK_CHECKER_URL = 'http://www.google.com';

    protected $proxy;

    protected $responseBody = false;
    protected $isPublic = false;
    protected $responseHeaders = false;
    protected $proxyType = null;

    public function __construct(Proxy $proxy)
    {
        $this->proxy = $proxy;
        $this->proxyType = $proxy->type;

    }

    public function checkType()
    {
        if(!$this->proxyType){

            foreach (self::$PROXY_TYPES as $item) {

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_PROXY, $this->proxy->proxy);
                curl_setopt($ch, CURLOPT_PROXYTYPE, $item);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_URL, self::$WORK_CHECKER_URL);
                $res = curl_exec($ch);

                curl_close($ch);

                if((bool) $res){
                    $this->proxyType = $item;
                    break;
                }
            }
        }
        return $this;
    }

    public function checkIp()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_PROXY, $this->proxy->proxy);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, self::$IP_CHECKER_URL);
        $res = curl_exec($ch);
        $info = curl_getinfo($ch);
        $body = substr($res, $info['header_size']);
        curl_close($ch);
        if(isset($info['header_size']) && $info['header_size'] > 0 ){
            $this->responseHeaders = true;
        }
        if((bool) $body){
            $this->responseBody = true;
            $ip = json_decode($body)->origin;
            $ips = explode(',', $ip);
            if (count($ips) > 1){
                $this->isPublic = true;
            }
        }
        return $this;
    }

    public function updateProxyStatus()
    {
        $this->proxy->update([
            'type' => $this->proxyType,
            'status' => $this->responseHeaders
                ? (($this->proxyType != 'CURLPROXY_SOCKS5' || $this->isPublic) ? 3 : 1)
                : 10,
        ]);
        return $this;
    }

    //  "url" => "https://www.google.com/"
    //  "content_type" => "text/html; charset=ISO-8859-1"
    //  "http_code" => 200
    //  "header_size" => 976
    //  "request_size" => 146
    //  "filetime" => -1
    //  "ssl_verify_result" => 0
    //  "redirect_count" => 0
    //  "total_time" => 6.312769
    //  "namelookup_time" => 7.3E-5
    //  "connect_time" => 3.327119
    //  "pretransfer_time" => 4.927631
    //  "size_upload" => 0.0
    //  "size_download" => 47500.0
    //  "speed_download" => 7525.0
    //  "speed_upload" => 0.0
    //  "download_content_length" => -1.0
    //  "upload_content_length" => -1.0
    //  "starttransfer_time" => 5.442591
    //  "redirect_time" => 0.0
    //  "redirect_url" => ""
    //  "primary_ip" => "200.219.152.226"
    //  "certinfo" => []
    //  "primary_port" => 3128
    //  "local_ip" => "172.19.0.7"
    //  "local_port" => 39432
    //  "http_version" => 3
    //  "protocol" => 2
    //  "ssl_verifyresult" => 0
    //  "scheme" => "HTTPS"
    //  "appconnect_time_us" => 4927425
    //  "connect_time_us" => 3327119
    //  "namelookup_time_us" => 73
    //  "pretransfer_time_us" => 4927631
    //  "redirect_time_us" => 0
    //  "starttransfer_time_us" => 5442591
    //  "total_time_us" => 6312769
}