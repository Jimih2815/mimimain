<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOptionImgToOptionValuesTable extends Migration
{
    public function up()
    {
        Schema::table('option_values', function (Blueprint $table) {
            // thêm cột lưu đường dẫn ảnh tuỳ chọn
            $table->string('option_img')->nullable()->after('extra_price');
        });
    }

    public function down()
    {
        Schema::table('option_values', function (Blueprint $table) {
            $table->dropColumn('option_img');
        });
    }
}
