<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // ⚠️ TẮT foreign key checks để TRUNCATE không lỗi
        Schema::disableForeignKeyConstraints();

        // Danh sách table bạn muốn reset
        $tables = [
            'product_option',
            'option_values',
            'option_types',
            'category_header_product',
            'category_headers',
            'category_product',
            'products',
            'categories',
            'cart_items',
        ];

        // Chỉ truncate những table đang có thực sự trong DB
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        // BẬT lại FK checks
        Schema::enableForeignKeyConstraints();

        //
        // === BẮT ĐẦU Seed dữ liệu “xịn” ===
        //

        // 1. Seed categories
        $categories = [];
        for ($i = 1; $i <= 5; $i++) {
            $name = ucfirst($faker->word);
            $categories[] = [
                'name'       => $name,
                'slug'       => Str::slug($name),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        DB::table('categories')->insert($categories);

        // 2. Seed products
        $products = [];
        for ($i = 1; $i <= 50; $i++) {
            $title = $faker->words(3, true);
            $products[] = [
                'name'        => ucfirst($title),
                'slug'        => Str::slug("{$title}-{$i}"),
                'description' => $faker->paragraph,
                'base_price'  => $faker->randomFloat(2, 10, 300),
                'img'         => $faker->imageUrl(640, 480, 'product', true),
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ];
        }
        DB::table('products')->insert($products);

        // Lấy ID
        $categoryIds = DB::table('categories')->pluck('id')->toArray();
        $productIds  = DB::table('products')->pluck('id')->toArray();

        // 3. Pivot category_product
        if (Schema::hasTable('category_product')) {
            $catProd = [];
            foreach ($productIds as $pid) {
                $cids = $faker->randomElements($categoryIds, rand(1, 3));
                foreach ($cids as $cid) {
                    $catProd[] = [
                        'category_id' => $cid,
                        'product_id'  => $pid,
                    ];
                }
            }
            DB::table('category_product')->insert($catProd);
        }

        // 4. Category headers + pivot nếu có
        if (Schema::hasTable('category_headers')) {
            $headers = [];
            foreach ($categoryIds as $cid) {
                for ($i = 1; $i <= 3; $i++) {
                    $headers[] = [
                        'category_id' => $cid,
                        'title'       => ucfirst($faker->words(2, true)),
                        'sort_order'  => $i,
                        'created_at'  => Carbon::now(),
                        'updated_at'  => Carbon::now(),
                    ];
                }
            }
            DB::table('category_headers')->insert($headers);

            $headerIds = DB::table('category_headers')->pluck('id')->toArray();
            if (Schema::hasTable('category_header_product')) {
                $chProd = [];
                foreach ($headerIds as $hid) {
                    foreach ($faker->randomElements($productIds, 2) as $pid) {
                        $chProd[] = [
                            'category_header_id' => $hid,
                            'product_id'         => $pid,
                        ];
                    }
                }
                DB::table('category_header_product')->insert($chProd);
            }
        }

        // 5. Option types & values
        $types = ['Size', 'Color', 'Material'];
        foreach ($types as $t) {
            DB::table('option_types')->insert([
                'name'       => $t,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        $typeIds = DB::table('option_types')->pluck('id')->toArray();

        $values = [];
        foreach ($typeIds as $tid) {
            for ($i = 1; $i <= 4; $i++) {
                $values[] = [
                    'option_type_id' => $tid,
                    'value'          => ucfirst($faker->word),
                    'extra_price'    => $faker->randomFloat(2, 0, 50),
                    'created_at'     => Carbon::now(),
                    'updated_at'     => Carbon::now(),
                ];
            }
        }
        DB::table('option_values')->insert($values);

        // 6. Pivot product_option
        $valueIds = DB::table('option_values')->pluck('id')->toArray();
        $prodOpt  = [];
        foreach ($productIds as $pid) {
            foreach ($faker->randomElements($valueIds, rand(1, 3)) as $vid) {
                $prodOpt[] = [
                    'product_id'      => $pid,
                    'option_value_id' => $vid,
                ];
            }
        }
        DB::table('product_option')->insert($prodOpt);

        // 7. Cart items (giả lập)
        if (Schema::hasTable('cart_items')) {
            $userIds = DB::table('users')->pluck('id')->toArray();
            $carts   = [];
            foreach ($userIds as $uid) {
                for ($i = 1; $i <= 5; $i++) {
                    $carts[] = [
                        'user_id'    => $uid,
                        'product_id' => $faker->randomElement($productIds),
                        'quantity'   => rand(1, 5),
                        'options'    => json_encode([
                            'option_value_id' => $faker->randomElement($valueIds)
                        ]),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }
            DB::table('cart_items')->insert($carts);
        }
    }
}
