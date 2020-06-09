<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('Название заказа');
            $table->text('description')->comment('Описание к заказу');
            $table->string('document')->nullable(true)->comment('Техническое задание');
            $table->unsignedTinyInteger('priority')->default(10)->comment('Приоритет обработки');
            $table->boolean('is_done')->default(0)->comment('Признак о выполнении');
            $table->timestamp('published_at')->nullable(true)->comment('Метка публикации заказа (доступ к обработке)');
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
        Schema::dropIfExists('orders');
    }
}
