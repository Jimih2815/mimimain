<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('chamcong')->create('attendance_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->date('work_date');
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->integer('total_minutes')->nullable();
            $table->string('status', 20)->default('pending');
            $table->dateTime('created_at')->nullable();

            $table->index(['user_id', 'work_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::connection('chamcong')->dropIfExists('attendance_requests');
    }
};
