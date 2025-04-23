<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('widgets', function(Blueprint $t){
            $t->id();
            $t->string('name')->comment('Tên hiển thị admin');
            $t->string('slug')->unique()->comment('Gọi widget: e.g. homepage_banner');
            $t->enum('type', ['banner','button','html'])->default('banner');
            $t->foreignId('collection_id')
              ->nullable()
              ->constrained()
              ->cascadeOnDelete();
            $t->string('image')->nullable()->comment('Đường dẫn banner hoặc ảnh button');
            $t->string('button_text')->nullable();
            $t->text('html')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('widgets');
    }
};
