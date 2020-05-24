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
            $table->string('proxy')->comment('name:pass@ip:port / ip:port');
            $table->string('type')->nullable(true)->comment('Тип прокси');
            $table->unsignedSmallInteger('status')->default(2)->comment('Надежность работы');
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
