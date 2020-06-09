<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_setting', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->comment('Внешний ключ к заказу');
            $table->unsignedBigInteger('setting_id')->comment('Внешний ключ к настройке');

//            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('CASCADE');
            $table->foreign('setting_id')->references('id')->on('settings')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_setting');
    }
}
