<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('market_id')->unique()->comment('Идентификатор продукта в маркете');
            $table->text('title', 500)->comment('Название продукта');
            $table->text('content')->comment('Характеристики');
            $table->text('link', 500)->comment('Ссылка на страницу продукта');
            $table->text('image', 500)->comment('Ссылка на картинку продукта');
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
        Schema::dropIfExists('products');
    }
}
