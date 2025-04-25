<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\MenuSection;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // share menu ra mọi view
        $menusections = MenuSection::with('groups.products')
                         ->orderBy('sort_order')
                         ->get();

        View::share('menusections', $menusections);
    }
}
