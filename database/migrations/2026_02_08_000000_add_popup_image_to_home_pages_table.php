<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('home_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('home_pages', 'popup_image')) {
                $table->string('popup_image')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('home_pages', function (Blueprint $table) {
            if (Schema::hasColumn('home_pages', 'popup_image')) {
                $table->dropColumn('popup_image');
            }
        });
    }
};
