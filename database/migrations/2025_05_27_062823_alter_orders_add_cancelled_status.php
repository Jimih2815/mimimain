<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterOrdersAddCancelledStatus extends Migration
{
    public function up()
    {
        // Thêm giá trị 'cancelled' vào ENUM
        DB::statement("
            ALTER TABLE `orders`
            MODIFY COLUMN `status`
            ENUM('pending','shipping','done','cancelled')
            NOT NULL
            DEFAULT 'pending'
        ");
    }

    public function down()
    {
        // Quay về chỉ còn 3 trạng thái cũ
        DB::statement("
            ALTER TABLE `orders`
            MODIFY COLUMN `status`
            ENUM('pending','shipping','done')
            NOT NULL
            DEFAULT 'pending'
        ");
    }
}
