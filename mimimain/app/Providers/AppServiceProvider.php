<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Với mỗi lần render partials.header, tự động truyền $menuCategories
        View::composer('partials.header', function ($view) {
            $categories = Category::with(['headers.products'])->get();
            $view->with('menuCategories', $categories);
        });
    }
}
