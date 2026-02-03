<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone', 20)->nullable()->unique()->after('name');
            });
        }

        if (Schema::hasColumn('users', 'email')) {
            // Make email optional (keeps unique index)
            DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['phone']);
                $table->dropColumn('phone');
            });
        }

        if (Schema::hasColumn('users', 'email')) {
            DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NOT NULL');
        }
    }
};
