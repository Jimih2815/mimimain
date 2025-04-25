<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('home_pages', function (Blueprint $table) {
            $table->string('button_text', 50)
                  ->nullable()
                  ->after('button_collection_id');
        });
    }

    public function down()
    {
        Schema::table('home_pages', function (Blueprint $table) {
            $table->dropColumn('button_text');
        });
    }
};
