<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // 1) Drop FK constraints
            $table->dropForeign(['user_id']);
            $table->dropForeign(['product_id']);

            // 2) Drop composite unique index
            $table->dropUnique('cart_items_user_id_product_id_unique');

            // 3) Re-create simple indexes & FKs
            $table->index('user_id');
            $table->index('product_id');

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Rollback: drop new FKs and indexes
            $table->dropForeign(['user_id']);
            $table->dropForeign(['product_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['product_id']);
            // Re-create the composite unique + original FKs
            $table->unique(['user_id', 'product_id'], 'cart_items_user_id_product_id_unique');
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->onDelete('cascade');
        });
    }
};
