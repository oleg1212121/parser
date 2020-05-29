<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysForTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->foreign('link_id')->references('id')->on('links')->onDelete('set null');
        });
        Schema::table('links', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn(['order_id']);
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['link_id']);
            $table->dropColumn(['link_id']);
        });
    }
}
