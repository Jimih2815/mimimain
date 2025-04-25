<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('category_groups', function (Blueprint $table) {
            // chỉ thêm nếu chưa có (chạy lại migrate không báo lỗi)
            if (!Schema::hasColumn('category_groups', 'menu_group_id')) {
                $table->unsignedBigInteger('menu_group_id')
                      ->nullable()
                      ->after('category_id');   // có thể bỏ ->after(...)
            }
        });
    }

    public function down(): void
    {
        Schema::table('category_groups', function (Blueprint $table) {
            if (Schema::hasColumn('category_groups', 'menu_group_id')) {
                $table->dropColumn('menu_group_id');
            }
        });
    }
};
