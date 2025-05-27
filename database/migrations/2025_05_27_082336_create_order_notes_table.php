<?php

// database/migrations/xxxx_xx_xx_create_order_notes_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateOrderNotesTable extends Migration
{
    public function up()
    {
        Schema::create('order_notes', function(Blueprint $table){
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_admin')->default(false);
            $table->text('message');
            $table->timestamps();
        });

        // Nếu bạn muốn migrate ghi chú cũ:
        DB::table('orders')->whereNotNull('note')->get()->each(function($o){
            DB::table('order_notes')->insert([
                'order_id'  => $o->id,
                'user_id'   => $o->user_id,
                'is_admin'  => false,
                'message'   => $o->note,
                'created_at'=> now(),
                'updated_at'=> now(),
            ]);
        });

        Schema::table('orders', function(Blueprint $t){
            $t->dropColumn('note');
        });
    }

    public function down()
    {
        Schema::table('orders', function(Blueprint $t){
            $t->string('note')->nullable();
        });
        Schema::dropIfExists('order_notes');
    }
}

