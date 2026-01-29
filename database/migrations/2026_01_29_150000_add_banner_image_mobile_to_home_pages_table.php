<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('home_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('home_pages', 'banner_image_mobile')) {
                $table->string('banner_image_mobile')->nullable()->after('banner_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('home_pages', function (Blueprint $table) {
            if (Schema::hasColumn('home_pages', 'banner_image_mobile')) {
                $table->dropColumn('banner_image_mobile');
            }
        });
    }
};
