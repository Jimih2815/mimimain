<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSectorsStructure extends Migration
{
    public function up()
    {
        // 1) Bỏ foreign key và cột collection_id
        Schema::table('sectors', function (Blueprint $table) {
            $table->dropForeign(['collection_id']);
            $table->dropColumn('collection_id');
            // Thêm slug để route friendly URL
            $table->string('slug')->after('name')->unique();
        });

        // 2) Tạo pivot table sector_collection
        Schema::create('sector_collection', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sector_id')->constrained()->onDelete('cascade');
            $table->foreignId('collection_id')->constrained()->onDelete('cascade');
            $table->string('custom_name')->nullable();       // tên hiển thị riêng cho collection trong sector
            $table->string('custom_image')->nullable();      // ảnh hiển thị riêng
            $table->integer('sort_order')->default(0);       // tuỳ chọn sắp xếp
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sector_collection');

        Schema::table('sectors', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->foreignId('collection_id')
                  ->constrained()
                  ->onDelete('cascade');
        });
    }
}
