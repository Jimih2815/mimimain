<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('category_header_product', function(Blueprint $t){
            $t->id();
            $t->foreignId('category_header_id')
              ->constrained('category_headers')
              ->cascadeOnDelete();
            $t->foreignId('product_id')
              ->constrained()
              ->cascadeOnDelete();
        });
    }

    public function down() {
        Schema::dropIfExists('category_header_product');
    }
};
