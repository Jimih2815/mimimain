<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('home_pages', function (Blueprint $table) {
            $table->id();
            $table->string('banner_image')->nullable();
            $table->timestamps();
        });

        // Seed record đầu tiên
        DB::table('home_pages')->insert([
            'banner_image' => null,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('home_pages');
    }
};
