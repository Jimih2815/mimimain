<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('help_requests', function (Blueprint $table) {
            $table->text('response')->nullable()->after('message');
            $table->enum('status', ['Đã tiếp nhận','Đang xử lý','Hoàn thành'])
                  ->default('Đã tiếp nhận')
                  ->after('response');
        });
    }

    public function down()
    {
        Schema::table('help_requests', function (Blueprint $table) {
            $table->dropColumn(['response','status']);
        });
    }
};
