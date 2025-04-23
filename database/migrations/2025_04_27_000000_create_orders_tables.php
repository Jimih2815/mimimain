<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1) Bảng orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('phone');
            $table->string('address');
            $table->text('note')->nullable();
            $table->enum('payment_method', ['cod', 'bank'])->default('cod');
            $table->string('bank_ref')->nullable();
            $table->enum('status', ['pending', 'paid', 'shipping', 'done', 'cancel'])
                  ->default('pending');
            $table->unsignedBigInteger('total')->default(0);
            $table->timestamps();    // created_at & updated_at cho orders
        });

        // 2) Bảng order_items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('price');  // đơn giá tại thời điểm mua
            $table->timestamps();    // ← quan trọng: thêm created_at & updated_at để khỏi lỗi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
