<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('home_pages', function (Blueprint $table) {
            $table->string('intro_text')->nullable();
            $table->string('intro_button_text')->nullable();
            $table->unsignedBigInteger('intro_button_collection_id')->nullable();

            $table->foreign('intro_button_collection_id')
                  ->references('id')->on('collections')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('home_pages', function (Blueprint $table) {
            $table->dropForeign(['intro_button_collection_id']);
            $table->dropColumn([
                'intro_text',
                'intro_button_text',
                'intro_button_collection_id',
            ]);
        });
    }
};
