<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImageProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_product', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->comment('Внешний ключ к продукту');
            $table->unsignedBigInteger('image_id')->comment('Внешний ключ к изображению');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('CASCADE');
            $table->foreign('image_id')->references('id')->on('images')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('image_product');
    }
}
