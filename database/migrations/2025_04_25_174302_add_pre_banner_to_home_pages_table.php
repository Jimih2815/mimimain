<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('home_pages', function (Blueprint $table) {
            $table->string('pre_banner_title')->nullable()->after('banner_image');
            $table->string('pre_banner_button_text')->nullable()->after('pre_banner_title');
            $table->foreignId('pre_banner_button_collection_id')
                  ->nullable()
                  ->constrained('collections')
                  ->nullOnDelete()
                  ->after('pre_banner_button_text');
        });
    }

    public function down()
    {
        Schema::table('home_pages', function (Blueprint $table) {
            $table->dropForeign(['pre_banner_button_collection_id']);
            $table->dropColumn([
              'pre_banner_button_collection_id',
              'pre_banner_button_text',
              'pre_banner_title',
            ]);
        });
    }
};
