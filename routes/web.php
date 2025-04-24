<?php

use Illuminate\Support\Facades\Route;

// PUBLIC CONTROLLERS
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CollectionController;

// AUTH CONTROLLERS
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;

// ADMIN CONTROLLERS
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController      as AdminProductController;
use App\Http\Controllers\Admin\UserController         as AdminUserController;
use App\Http\Controllers\Admin\HomePageController;
use App\Http\Controllers\Admin\CollectionController   as AdminCollectionController;
use App\Http\Controllers\Admin\WidgetController;
use App\Http\Controllers\Admin\WidgetPlacementController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Guest-only
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])
         ->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    Route::get('login',    [LoginController::class,     'showLoginForm'])->name('login');
    Route::post('login',   [LoginController::class,     'login']);
});

// Home dynamic
Route::get('/', [HomeController::class, 'index'])->name('home');

// Products
Route::get('/products',        [ProductController::class, 'index'])       ->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])        ->name('products.show');

// Categories
Route::get('/categories',                 [CategoryController::class, 'index']) ->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])  ->name('categories.show');

// Cart
Route::get('/cart',               [CartController::class, 'index'])  ->name('cart.index');
Route::post('/cart/add/{id}',     [CartController::class, 'add'])    ->name('cart.add');
Route::post('/cart/remove/{key}', [CartController::class, 'remove']) ->name('cart.remove');

// Checkout
Route::get('/checkout',                 [CheckoutController::class, 'show'])       ->name('checkout.show');
Route::post('/checkout/bank-ref',       [CheckoutController::class, 'ajaxBankRef'])->name('checkout.bankRef');
Route::post('/checkout/confirm',        [CheckoutController::class, 'confirm'])    ->name('checkout.confirm');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])    ->name('checkout.success');

// Profile & Logout
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::middleware('auth')->group(function(){
    Route::get('/profile', [ProfileController::class, 'edit'])   ->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update']) ->name('profile.update');
});

// Collections public
Route::get('/collections/{slug}', [CollectionController::class,'show'])
     ->name('collections.show');


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
     // ->middleware('auth')
     ->name('admin.')
     ->group(function(){

    // Mega-menu
    Route::get    ('menu',                       [MenuController::class,'index'])              ->name('menu.index');
    Route::post   ('menu/section',               [MenuController::class,'storeSection'])       ->name('menu.section.store');
    Route::put    ('menu/section/{section}',     [MenuController::class,'updateSection'])      ->name('menu.section.update');
    Route::delete ('menu/section/{section}',     [MenuController::class,'destroySection'])     ->name('menu.section.destroy');
    Route::post   ('menu/section/{section}/group',[MenuController::class,'storeGroup'])         ->name('menu.group.store');
    Route::put    ('menu/group/{group}',         [MenuController::class,'updateGroup'])        ->name('menu.group.update');
    Route::delete ('menu/group/{group}',         [MenuController::class,'destroyGroup'])       ->name('menu.group.destroy');
    Route::post   ('menu/group/{group}/product', [MenuController::class,'addProductToGroup']) ->name('menu.group.product.add');
    Route::delete ('menu/group/{group}/product/{pid}', [MenuController::class,'removeProductFromGroup'])
                                                     ->name('menu.group.product.remove');

    // Orders
    Route::get('orders', [OrderController::class,'index'])->name('orders.index');

    // Products CRUD
    Route::resource('products', AdminProductController::class);

    // Users
    Route::get   ('users',                     [AdminUserController::class,'index'])         ->name('users.index');
    Route::post  ('users/{user}/reset-password',[AdminUserController::class,'resetPassword'])->name('users.resetPassword');
    Route::get   ('users/{user}',              [AdminUserController::class,'show'])          ->name('users.show');
    Route::delete('users/{user}',              [AdminUserController::class,'destroy'])       ->name('users.destroy');

    // Collections, Widgets, Placements
    Route::resource('collections', AdminCollectionController::class);
    Route::resource('widgets',     WidgetController::class);
    Route::resource('placements',  WidgetPlacementController::class);

    // Home banner
    Route::get ('home-banner', [HomePageController::class,'edit'])   ->name('home.edit');
    Route::post('home-banner', [HomePageController::class,'update']) ->name('home.update');
});
