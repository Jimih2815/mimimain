<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Category;
use App\Models\MenuSection;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
{
    // Lấy menu section mới (MenuSection -> items)
    $menuSections = MenuSection::with('items')
                    ->orderBy('sort_order')
                    ->get();

    View::share('menuSections', $menuSections);
}
}
