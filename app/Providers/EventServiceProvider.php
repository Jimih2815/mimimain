<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;                    // < thêm
use App\Listeners\MergeCartAfterLogin;               // < thêm
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Sơ đồ Event ➜ Listener của app.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Mail verify mặc định của Breeze/Laravel
        \Illuminate\Auth\Events\Registered::class => [
            \Illuminate\Auth\Listeners\SendEmailVerificationNotification::class,
        ],

        // Khi user login, gộp giỏ hàng từ DB vào session
        Login::class => [
            MergeCartAfterLogin::class,
        ],
    ];

    /**
     * Đăng ký các event cho ứng dụng.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Xác định có tự động discover các event listener hay không.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}