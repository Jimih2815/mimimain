<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSectorsStructure extends Migration
{
    public function up()
    {
        // Tạo bảng pivot sector_collection
        Schema::create('sector_collection', function (Blueprint $table) {
            $table->foreignId('sector_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->foreignId('collection_id')
                  ->constrained()
                  ->onDelete('cascade');

            // các trường tuỳ chỉnh cho pivot
            $table->string('custom_name')->nullable();
            $table->string('custom_image')->nullable();
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            // Khóa chính kép để không bị duplicate
            $table->primary(['sector_id', 'collection_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sector_collection');
    }
}
