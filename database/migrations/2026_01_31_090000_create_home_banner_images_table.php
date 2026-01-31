<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('home_banner_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_page_id')->nullable()->constrained('home_pages')->nullOnDelete();
            $table->string('device')->default('desktop'); // desktop | mobile
            $table->string('image');
            $table->foreignId('collection_id')->nullable()->constrained('collections')->nullOnDelete();
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();

            $table->index(['device', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_banner_images');
    }
};
