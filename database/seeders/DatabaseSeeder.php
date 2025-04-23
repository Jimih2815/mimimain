<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Chạy tất cả các seeder của bạn ở đây.
     *
     * @return void
     */
    public function run(): void
    {
        // Nếu bạn đã có nhiều seeder khác, gọi lần lượt ở đây:
        // $this->call(OtherSeeder::class);

        // Gọi TestDataSeeder để đổ ngập dữ liệu test
        $this->call(TestDataSeeder::class);
    }
}
