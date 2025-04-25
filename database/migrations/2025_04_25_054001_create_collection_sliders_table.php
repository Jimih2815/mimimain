<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('collection_sliders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')
                  ->constrained()         // ép khóa ngoài tới collections.id
                  ->cascadeOnDelete();
            $table->string('image');      // đường dẫn storage/app/public/…
            $table->string('text');       // caption dưới ảnh
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('collection_sliders');
    }
};
