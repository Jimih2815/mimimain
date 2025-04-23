<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng users.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                    // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('name');                         // tên user
            $table->string('email')->unique();              // email, duy nhất
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');                     // mật khẩu
            $table->rememberToken();                        // trường remember_token
            $table->timestamps();                           // created_at & updated_at
        });
    }

    /**
     * Xóa bảng users khi rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
