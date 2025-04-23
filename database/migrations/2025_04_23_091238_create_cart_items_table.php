<?php
// database/migrations/2025_04_23_091238_create_cart_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            // “xương sườn” bị mất nè
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->unsignedInteger('quantity');

            // các lựa chọn như “Màu”, “Size”… lưu JSON
            $table->json('options')->nullable();

            $table->timestamps();
            $table->unique(['user_id', 'product_id']); // 1 user - 1 sp duy nhất
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
