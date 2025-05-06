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

    public function boot()
{
    View::composer('partials.mobile-header', function($view){
        // Cart từ session
        $cart = session('cart', []);
        $cartCount = array_sum(array_column($cart, 'quantity'));

        // Sections → Groups → Products
        $sections = MenuSection::with('groups.products')
                       ->orderBy('sort_order')->get();

        $view->with(compact('cart','cartCount','sections'));
    });
}
}
