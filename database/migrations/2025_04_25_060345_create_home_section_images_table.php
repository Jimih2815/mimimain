<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('home_section_images', function (Blueprint $t) {
            $t->id();
            $t->tinyInteger('position')->default(0);         // 0 hoặc 1
            $t->string('image')->nullable();                 // đường dẫn trong storage
            $t->foreignId('collection_id')                   // link đến collection
              ->nullable()->constrained()->nullOnDelete();
            $t->timestamps();
        });

        // Seed sẵn 2 vị trí
        DB::table('home_section_images')->insert([
            ['position'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['position'=>1,'created_at'=>now(),'updated_at'=>now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('home_section_images');
    }
};
