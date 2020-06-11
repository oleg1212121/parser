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
        $data = file_get_contents(base_path().'/proxies.txt');
        $proxies = [];
        if($data){
            $data = explode(PHP_EOL, $data);
            foreach ($data as $k => $item) {
                $i = explode('+++',$item);
                $date = now()->format('Y-m-d H:i:s');
                if(count($i) > 1){
                    array_push($proxies, [
                        'proxy' => $i[0],
                        'type' => $i[1],
                        'status' => $i[2],
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                }
                unset($item);
            }
            \DB::table('proxies')->insert($proxies);
        }else{
            $this->command->info('Файл со списком прокси не найден, либо произошла ошибка.');
        }
    }
}
