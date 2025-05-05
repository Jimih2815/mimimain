<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('favorite_product', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')
              ->constrained()
              ->onDelete('cascade');
        $table->foreignId('product_id')
              ->constrained()
              ->onDelete('cascade');
        $table->timestamps();

        // đảm bảo mỗi cặp user–product chỉ tồn tại 1 lần
        $table->unique(['user_id','product_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorite_product');
    }
};
