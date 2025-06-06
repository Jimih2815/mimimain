<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('widget_placements', function(Blueprint $t){
            $t->string('region')->primary();
            $t->foreignId('widget_id')
              ->constrained()
              ->cascadeOnDelete();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('widget_placements');
    }
};
