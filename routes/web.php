<?php

use Illuminate\Support\Facades\Route;

// PUBLIC CONTROLLERS
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

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
use App\Http\Controllers\Admin\CollectionSliderController;
use App\Http\Controllers\Admin\HomeSectionImageController;
use App\Http\Controllers\Admin\ProductSliderController;
use App\Http\Controllers\Admin\WidgetController;
use App\Http\Controllers\Admin\WidgetPlacementController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Đăng ký, đăng nhập (guest only)
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])
         ->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    Route::get('login',    [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login',   [LoginController::class, 'login']);
});

// Home động
Route::get('/', [HomeController::class, 'index'])->name('home');

// Products
Route::get('/products',        [ProductController::class, 'index']) ->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])  ->name('products.show');

// Collections public
Route::get('/collections/{slug}', [CollectionController::class, 'show'])
     ->name('collections.show');

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

// Logout (nên giữ ở ngoài auth để form logout luôn hoạt động)
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Profile (auth only)
Route::middleware('auth')->group(function(){
    Route::get('/profile', [ProfileController::class, 'edit'])   ->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update']) ->name('profile.update');
});


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
|
| Prefix: /admin
| Name prefix: admin.
| (có thể bật middleware auth nếu muốn)
*/
Route::prefix('admin')
     //->middleware('auth')
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
    Route::delete ('menu/group/{group}/product/{pid}', 
                                          [MenuController::class,'removeProductFromGroup'])
                                                     ->name('menu.group.product.remove');

    // Orders
    Route::get('orders', [OrderController::class,'index'])->name('orders.index');

    // Products CRUD
    Route::resource('products', AdminProductController::class);

    // Users
    Route::get   ('users',                       [AdminUserController::class,'index'])         ->name('users.index');
    Route::post  ('users/{user}/reset-password',[AdminUserController::class,'resetPassword'])->name('users.resetPassword');
    Route::get   ('users/{user}',                [AdminUserController::class,'show'])          ->name('users.show');
    Route::delete('users/{user}',                [AdminUserController::class,'destroy'])       ->name('users.destroy');

    // Collections & Widgets
    Route::resource('collections', AdminCollectionController::class);
    Route::resource('widgets',     WidgetController::class);
    Route::resource('placements',  WidgetPlacementController::class);

    // Home banner (HomePage)
    Route::get ('home-banner', [HomePageController::class,'edit'])   ->name('home.edit');
    Route::post('home-banner', [HomePageController::class,'update']) ->name('home.update');

    // Collection sliders (Admin)
    Route::resource('collection-sliders', CollectionSliderController::class)
         ->except(['show']);
    // thêm route move up/down
    Route::post('collection-sliders/{collectionSlider}/move', 
        [CollectionSliderController::class,'move'])
        ->name('collection-sliders.move');

    // Home section images (2 ảnh cạnh nhau)
    Route::get  ('home-section-images', [HomeSectionImageController::class,'edit'])
         ->name('home.images.edit');
    Route::post ('home-section-images', [HomeSectionImageController::class,'update'])
         ->name('home.images.update');

    // Product sliders (Admin)
    Route::resource('product-sliders', ProductSliderController::class)
         ->only(['index','create','store','edit','update','destroy']);
});
