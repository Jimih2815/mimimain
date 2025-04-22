<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\HeaderController;

// Trang chủ
Route::view('/', 'home')->name('home');

// Danh sách & tìm kiếm sản phẩm
Route::get('/products', [ProductController::class, 'index'])
     ->name('products.index');

// Chi tiết sản phẩm theo slug
Route::get('/products/{slug}', [ProductController::class, 'show'])
     ->name('products.show');

// Trang danh mục sản phẩm
Route::get('/categories', [CategoryController::class, 'index'])
     ->name('categories.index');

// Giỏ hàng
Route::get('/cart',                  [CartController::class, 'index'])   ->name('cart.index');
Route::post('/cart/add/{id}',        [CartController::class, 'add'])     ->name('cart.add');
Route::post('/cart/remove/{id}',     [CartController::class, 'remove'])  ->name('cart.remove');

// Thanh toán
Route::post('/checkout',             [CheckoutController::class, 'show'])    ->name('checkout.show');
Route::post('/checkout/process',     [CheckoutController::class, 'process']) ->name('checkout.process');

// Quản lý Mega‑Menu Header (Admin, tạm chưa auth)
Route::prefix('admin')->name('admin.')->group(function(){
    Route::get(   'headers',                       [HeaderController::class,'index'])         ->name('headers.index');
    Route::post(  'headers',                       [HeaderController::class,'store'])         ->name('headers.store');
    Route::put(   'headers/{header}',              [HeaderController::class,'update'])        ->name('headers.update');
    Route::delete('headers/{header}',              [HeaderController::class,'destroy'])       ->name('headers.destroy');
    Route::post(  'headers/{header}/product',      [HeaderController::class,'addProduct'])    ->name('headers.product.add');
    Route::delete('headers/{header}/product/{pid}',[HeaderController::class,'removeProduct']) ->name('headers.product.remove');
});
