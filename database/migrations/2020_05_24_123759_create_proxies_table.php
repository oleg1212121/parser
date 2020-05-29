<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProxiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proxies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('proxy')->unique()->comment('name:pass@ip:port / ip:port');
            $table->string('type')->nullable(true)->comment('Тип прокси');
            $table->smallInteger('fails')->default(0)->comment('Количество неудачных использований');
            $table->smallInteger('used')->default(0)->comment('Количество использований');
            $table->unsignedSmallInteger('status')->default(3)->comment('Надежность работы');
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
        Schema::dropIfExists('proxies');
    }
}
