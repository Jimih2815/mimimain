<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Lấy menuCategories 1 lần, share cho tất cả view
        $menuCategories = Category::with(['headers.products'])
                                  ->orderBy('name')
                                  ->get();

        View::share('menuCategories', $menuCategories);
    }
}
