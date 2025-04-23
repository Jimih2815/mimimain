<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\HeaderController;
use App\Http\Controllers\ProfileController;

// 1) Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// 2) Auth (Breeze) routes
require __DIR__.'/auth.php';
Route::get('/dashboard',
    fn() => view('dashboard')
)->middleware(['auth'])->name('dashboard');
// 3) Dashboard & profile (chỉ truy cập khi đã login)
Route::middleware(['auth','verified'])->group(function(){
    Route::view('/dashboard','dashboard')->name('dashboard');

    Route::get('/profile',   [ProfileController::class,'edit'])   ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class,'update']) ->name('profile.update');
    Route::delete('/profile',[ProfileController::class,'destroy'])->name('profile.destroy');
});

// 4) Sản phẩm
Route::get('/products',        [ProductController::class,'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class,'show']) ->name('products.show');

// 5) Danh mục
Route::get('/categories', [CategoryController::class,'index'])->name('categories.index');

// 6) Giỏ hàng
Route::get( '/cart',              [CartController::class,'index']) ->name('cart.index');
Route::post('/cart/add/{id}',     [CartController::class,'add'])   ->name('cart.add');
Route::post('/cart/remove/{id}',  [CartController::class,'remove'])->name('cart.remove');

// 7) Checkout
Route::post('/checkout',         [CheckoutController::class,'show'])   ->name('checkout.show');
Route::post('/checkout/process', [CheckoutController::class,'process'])->name('checkout.process');

// 8) Admin Mega-Menu Header (tạm chưa auth)
Route::prefix('admin')->name('admin.')->group(function(){
    Route::get(   '/headers',                        [HeaderController::class,'index'])            ->name('headers.index');
    Route::post(  '/headers',                        [HeaderController::class,'store'])            ->name('headers.store');
    Route::put(   '/headers/{header}',               [HeaderController::class,'update'])           ->name('headers.update');
    Route::delete('/headers/{header}',               [HeaderController::class,'destroy'])          ->name('headers.destroy');
    Route::post(  '/headers/{header}/product',       [HeaderController::class,'addProduct'])       ->name('headers.product.add');
    Route::delete('/headers/{header}/product/{pid}', [HeaderController::class,'removeProduct'])    ->name('headers.product.remove');
});
