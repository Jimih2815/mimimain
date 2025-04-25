<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_group_product', function (Blueprint $table) {
            $table->id();

            // N → N giữa group và product
            $table->foreignId('category_group_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->unsignedInteger('sort_order')    // sắp xếp SP trong dropdown
                  ->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_group_product');
    }
};
