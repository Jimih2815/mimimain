<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('sidebar_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('sidebar_items')
                  ->cascadeOnDelete();
            $table->foreignId('collection_id')
                  ->nullable()
                  ->constrained('collections') // nếu bạn đã có table collections
                  ->nullOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sidebar_items');
    }
};
