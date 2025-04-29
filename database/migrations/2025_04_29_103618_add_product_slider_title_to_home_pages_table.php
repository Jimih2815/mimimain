<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('home_pages', function (Blueprint $table) {
            // Thêm sau collection_section_button_collection_id (tuỳ bạn)
            $table->string('product_slider_title')->nullable()
                  ->after('collection_slider_title');
        });
    }

    public function down(): void
    {
        Schema::table('home_pages', function (Blueprint $table) {
            $table->dropColumn('product_slider_title');
        });
    }
};
