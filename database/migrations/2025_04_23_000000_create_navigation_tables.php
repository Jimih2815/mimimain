<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menu_sections', function (Blueprint $t) {
            $t->id();
            $t->string('name');       // ĐÈN NGỦ, GẤU BÔNG…
            $t->string('slug')->unique();
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('menu_section_id')->constrained()->cascadeOnDelete();
            $t->string('label');      // text hiển thị trong dropdown
            $t->string('url');        // đường dẫn
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menu_sections');
    }
};
