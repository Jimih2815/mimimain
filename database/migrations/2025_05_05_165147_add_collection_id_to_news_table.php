<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollectionIdToNewsTable extends Migration
{
    public function up()
    {
        Schema::table('news', function (Blueprint $table) {
            // thêm cột collection_id nullable, sau cột content (nếu có)
            $table->unsignedBigInteger('collection_id')
                  ->nullable()
                  ->after('content');

            // nếu muốn ràng buộc khoá ngoại:
            $table->foreign('collection_id')
                  ->references('id')
                  ->on('collections')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('news', function (Blueprint $table) {
            // drop foreign key trước
            $table->dropForeign(['collection_id']);
            // rồi drop cột
            $table->dropColumn('collection_id');
        });
    }
}
