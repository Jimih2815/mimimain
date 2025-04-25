<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('home_pages', function (Blueprint $table) {
            $table->boolean('show_button')
                  ->default(false)
                  ->after('about_text');
            $table->foreignId('button_collection_id')
                  ->nullable()
                  ->constrained('collections')
                  ->nullOnDelete()
                  ->after('show_button');
        });
    }

    public function down()
    {
        Schema::table('home_pages', function (Blueprint $table) {
            $table->dropForeign(['button_collection_id']);
            $table->dropColumn(['button_collection_id', 'show_button']);
        });
    }
};
