<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('link',500)->comment('Ссылка для парсинга');
            $table->unsignedTinyInteger('type')->default(0)->comment('Тип ссылки каталог/продукт');
            $table->boolean('is_done')->default(0)->comment('Успешность завершения скачивания');
            $table->unsignedBigInteger('order_id')->nullable(true)->comment('Ключ к заказу');
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
        Schema::dropIfExists('links');
    }
}
