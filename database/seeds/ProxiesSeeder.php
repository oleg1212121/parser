<?php

use Illuminate\Database\Seeder;

class ProxiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $proxies = [
            [
                'proxy' => 'cnctkloo-1:nd3bfe4cxtk4@45.94.47.66:1080', // +
                'status' => 1,
                'type' => 'CURLPROXY_SOCKS5',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'proxy' => 'cnctkloo-2:nd3bfe4cxtk4@193.8.94.225:1080', // +
                'status' => 1,
                'type' => 'CURLPROXY_SOCKS5',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'proxy' => 'cnctkloo-3:nd3bfe4cxtk4@45.94.47.108:1080', // +
                'status' => 1,
                'type' => 'CURLPROXY_SOCKS5',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'proxy' => 'cnctkloo-4:nd3bfe4cxtk4@45.141.176.202:1080', // +
                'status' => 1,
                'type' => 'CURLPROXY_SOCKS5',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'proxy' => 'cnctkloo-5:nd3bfe4cxtk4@193.8.56.119:1080', // +
                'status' => 1,
                'type' => 'CURLPROXY_SOCKS5',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'proxy' => 'cnctkloo-6:nd3bfe4cxtk4@194.33.29.86:1080', // +
                'status' => 1,
                'type' => 'CURLPROXY_SOCKS5',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'proxy' => 'cnctkloo-7:nd3bfe4cxtk4@85.209.130.129:1080', // +
                'status' => 1,
                'type' => 'CURLPROXY_SOCKS5',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'proxy' => 'cnctkloo-8:nd3bfe4cxtk4@193.8.215.135:1080', // +
                'status' => 1,
                'type' => 'CURLPROXY_SOCKS5',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'proxy' => 'cnctkloo-9:nd3bfe4cxtk4@193.27.23.190:1080', // +
                'status' => 1,
                'type' => 'CURLPROXY_SOCKS5',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'proxy' => 'cnctkloo-10:nd3bfe4cxtk4@85.209.129.161:1080', // +
                'status' => 1,
                'type' => 'CURLPROXY_SOCKS5',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
        ];


        \DB::table('proxies')->insert($proxies);
    }
}
