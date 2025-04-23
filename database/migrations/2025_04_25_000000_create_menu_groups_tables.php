<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Bảng Group (mục con của Section)
        Schema::create('menu_groups', function (Blueprint $t) {
            $t->id();
            $t->foreignId('menu_section_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        // Pivot Group ⇄ Product
        Schema::create('menu_group_product', function (Blueprint $t) {
            $t->id();
            $t->foreignId('menu_group_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('sort_order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_group_product');
        Schema::dropIfExists('menu_groups');
    }
};
