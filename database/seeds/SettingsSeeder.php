<?php

use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Setting::firstOrCreate([
            'name' => 'images',
            'slug' => 'Сохранение картинок товара'
        ]);
    }
}
