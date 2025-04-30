<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('menu_sections', function (Blueprint $table) {
            $table->foreignId('collection_id')
                  ->nullable()
                  ->after('sort_order')
                  ->constrained('collections')
                  ->nullOnDelete();
        });
    }
    
    public function down()
    {
        Schema::table('menu_sections', function (Blueprint $table) {
            $table->dropForeign(['collection_id']);
            $table->dropColumn('collection_id');
        });
    }

};
