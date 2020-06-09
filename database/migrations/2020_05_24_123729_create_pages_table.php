<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('content')->comment('Контент скачанной страницы');
            $table->unsignedTinyInteger('type')->default(0)->comment('Тип страницы');
            $table->boolean('is_done')->default(0)->comment('Отметка об обработке текущей страницы');
            $table->unsignedBigInteger('link_id')->nullable(true)->comment('Ключ к ссылке');
            $table->unsignedBigInteger('order_id')->comment('Ключ к заказу');
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
        Schema::dropIfExists('pages');
    }
}
