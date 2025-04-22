<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('category_headers', function(Blueprint $t){
            $t->id();
            $t->foreignId('category_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->integer('sort_order')->default(0);
            $t->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('category_headers');
    }
};
