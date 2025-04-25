<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('category_groups', function (Blueprint $table) {
            // Cho phép NULL trở lại
            $table->unsignedBigInteger('menu_group_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('category_groups', function (Blueprint $table) {
            // rollback: bắt buộc NOT NULL
            $table->unsignedBigInteger('menu_group_id')->nullable(false)->change();
        });
    }
};
