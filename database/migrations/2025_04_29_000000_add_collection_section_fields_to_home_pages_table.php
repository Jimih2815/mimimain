<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('home_pages', function (Blueprint $table) {
            $table->string('collection_section_title')->nullable();
            $table->string('collection_section_button_text')->nullable();
            $table->unsignedBigInteger('collection_section_button_collection_id')->nullable();

            $table->foreign('collection_section_button_collection_id')
                  ->references('id')->on('collections')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('home_pages', function (Blueprint $table) {
            $table->dropForeign(['collection_section_button_collection_id']);
            $table->dropColumn([
                'collection_section_title',
                'collection_section_button_text',
                'collection_section_button_collection_id',
            ]);
        });
    }
};
