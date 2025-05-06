<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Thêm cột order_code 8 ký tự, unique
            $table->string('order_code', 8)
                  ->nullable()   // với đơn cũ còn null
                  ->unique()
                  ->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['order_code']);
            $table->dropColumn('order_code');
        });
    }
};
