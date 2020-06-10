<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',32)->unique()->comment('Имя файла на диске. Генерируется из ссылки md5');
            $table->text('link', 500)->comment('Ссылка на изображение');
            $table->string('extention')->default('.jpeg')->comment('Расширение файла');
            $table->boolean('is_done')->default(0)->comment('Отметка о завершении скачивания');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
