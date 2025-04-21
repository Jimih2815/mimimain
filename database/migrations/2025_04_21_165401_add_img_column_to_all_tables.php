<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // categories
        Schema::table('categories', function (Blueprint $table) {
            $table->string('img')->nullable()->after('updated_at');
        });

        // products
        Schema::table('products', function (Blueprint $table) {
            $table->string('img')->nullable()->after('updated_at');
        });

        // classifications
        Schema::table('classifications', function (Blueprint $table) {
            $table->string('img')->nullable()->after('updated_at');
        });

        // options
        Schema::table('options', function (Blueprint $table) {
            $table->string('img')->nullable()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('img');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('img');
        });
        Schema::table('classifications', function (Blueprint $table) {
            $table->dropColumn('img');
        });
        Schema::table('options', function (Blueprint $table) {
            $table->dropColumn('img');
        });
    }
};
