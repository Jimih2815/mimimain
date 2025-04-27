<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. IMPORT CONTROLLERS
|--------------------------------------------------------------------------
|
| Phân chia theo nhóm: PUBLIC, AUTH, ADMIN
*/

//
// PUBLIC CONTROLLERS
//
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

//
// AUTH CONTROLLERS
//
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;

//
// ADMIN CONTROLLERS
//
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController         as AdminProductController;
use App\Http\Controllers\Admin\CategoryController        as AdminCategoryController;
use App\Http\Controllers\Admin\CategoryGroupController;
use App\Http\Controllers\Admin\UserController            as AdminUserController;
use App\Http\Controllers\Admin\HomePageController;
use App\Http\Controllers\Admin\CollectionController      as AdminCollectionController;
use App\Http\Controllers\Admin\CollectionSliderController;
use App\Http\Controllers\Admin\HomeSectionImageController;
use App\Http\Controllers\Admin\ProductSliderController;
use App\Http\Controllers\Admin\WidgetController;
use App\Http\Controllers\Admin\WidgetPlacementController;

/*
|--------------------------------------------------------------------------
| 2. PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// 2.1 Trang chủ & catalog
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products',        [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/collections/{slug}', [CollectionController::class, 'show'])->name('collections.show');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// 2.2 Cart & Checkout
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get  ('/',            [CartController::class,'index'])->name('index');
    Route::post ('add/{id}',     [CartController::class,'add'])->name('add');
    Route::post ('remove/{k}',   [CartController::class,'remove'])->name('remove');
    Route::post ('update/{key}', [CartController::class,'update'])->name('update');
});

Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get  ('/',               [CheckoutController::class,'show'])->name('show');
    Route::post ('bank-ref',        [CheckoutController::class,'ajaxBankRef'])->name('bankRef');
    Route::post ('confirm',         [CheckoutController::class,'confirm'])->name('confirm');
    Route::get  ('success/{order}', [CheckoutController::class,'success'])->name('success');
});

/*
|--------------------------------------------------------------------------
| 3. AUTH ROUTES
|--------------------------------------------------------------------------
*/

// 3.1 Cho khách (guest) – đăng ký, đăng nhập
Route::middleware('guest')->group(function () {
    Route::get ('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    Route::get ('login',    [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login',    [LoginController::class, 'login']);
});

// 3.2 Cho user đã login – profile, logout
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get  ('profile', [ProfileController::class,'edit'])->name('profile.edit');
    Route::put  ('profile', [ProfileController::class,'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| 4. ADMIN ROUTES
|--------------------------------------------------------------------------
|
| Prefix:        /admin
| Name prefix:   admin.
| Middleware:    auth (và role-check nếu cần)
*/
Route::prefix('admin')
     // ->middleware('auth')
     ->name('admin.')
     ->group(function () {

    // 4.1 Dashboard
    Route::get('/', fn() => view('admin.dashboard'))->name('dashboard');

    // 4.2 Mega‐menu
    Route::controller(MenuController::class)->group(function () {
        Route::get   ('menu',                         'index')->name('menu.index');
        Route::post  ('menu/section',                 'storeSection')->name('menu.section.store');
        Route::put   ('menu/section/{section}',       'updateSection')->name('menu.section.update');
        Route::delete('menu/section/{section}',       'destroySection')->name('menu.section.destroy');
        Route::post  ('menu/section/{section}/group', 'storeGroup')->name('menu.group.store');
        Route::put   ('menu/group/{group}',           'updateGroup')->name('menu.group.update');
        Route::delete('menu/group/{group}',           'destroyGroup')->name('menu.group.destroy');
        Route::post  ('menu/group/{group}/product',   'addProductToGroup')->name('menu.group.product.add');
        Route::delete('menu/group/{group}/product/{pid}', 'removeProductFromGroup')->name('menu.group.product.remove');
    });

    // 4.3 Quản lý đơn hàng
    Route::resource('orders', OrderController::class)
         ->only(['index','show','destroy']);

    // 4.4 Quản lý sản phẩm
    Route::resource('products', AdminProductController::class);

    // 4.5 Quản lý danh mục & nhóm dropdown
    Route::resource('categories', AdminCategoryController::class)
         ->except(['show']);
    Route::post   ('categories/{category}/groups',       [CategoryGroupController::class,'store'])->name('categories.groups.store');
    Route::put    ('categories/groups/{group}',          [CategoryGroupController::class,'update'])->name('categories.groups.update');
    Route::delete ('categories/groups/{group}',          [CategoryGroupController::class,'destroy'])->name('categories.groups.destroy');
    Route::post   ('categories/groups/{group}/product',  [CategoryGroupController::class,'attachProduct'])->name('categories.groups.product.attach');
    Route::delete ('categories/groups/{group}/product/{id}', [CategoryGroupController::class,'detachProduct'])->name('categories.groups.product.detach');

    // 4.6 Quản lý người dùng
    Route::controller(AdminUserController::class)->group(function () {
        Route::get   ('users',                      'index')->name('users.index');
        Route::get   ('users/{user}',               'show')->name('users.show');
        Route::post  ('users/{user}/reset-password','resetPassword')->name('users.resetPassword');
        Route::delete('users/{user}',               'destroy')->name('users.destroy');
    });

    // 4.7 Collections & Collection Sliders
    Route::resource('collections', AdminCollectionController::class);

    // Định nghĩa route move trước khi resource để tránh trùng tên
    Route::post('collection-sliders/{collection_slider}/move', 
         [CollectionSliderController::class,'move'])
         ->name('collection-sliders.move');

    Route::resource('collection-sliders', CollectionSliderController::class)
         ->except(['show']);

    // 4.8 Home Page (banner) & Home Section Images
    Route::get ('home',                  [HomePageController::class,'edit'])->name('home.edit');
    Route::post('home',                  [HomePageController::class,'update'])->name('home.update');
    Route::get ('home-section-images',   [HomeSectionImageController::class,'index'])->name('home-section-images.index');
    Route::post('home-section-images',   [HomeSectionImageController::class,'update'])->name('home-section-images.update');

    // 4.9 Product Sliders
    Route::resource('product-sliders', ProductSliderController::class)
         ->only(['index','create','store','edit','update','destroy']);

    // 4.10 Widgets & Widget Placements
    Route::resource('widgets',           WidgetController::class);
    Route::resource('placements', WidgetPlacementController::class);
});

// Redirect khi có dấu slash thừa ở cuối URL
Route::get('{any}/', function () {
    return redirect(rtrim(request()->getRequestUri(), '/'));
})->where('any', '.*');
