<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeys extends Migration
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
            $table->foreign('parent_id')->references('id')->on('links')->onDelete('set null');
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
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id']);
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['link_id']);
            $table->dropColumn(['link_id']);
        });
    }
}
