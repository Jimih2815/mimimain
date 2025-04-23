<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\HeaderController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\MenuController;
// Trang chủ
Route::view('/', 'home')->name('home');
// Auth – Đăng ký
Route::get('register', [RegisterController::class, 'showRegistrationForm'])
     ->name('register');
Route::post('register', [RegisterController::class, 'register']);

// Auth – Đăng nhập (tương tự)
Route::get('login', [LoginController::class, 'showLoginForm'])
     ->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])
     ->name('logout');
// Danh sách & tìm kiếm sản phẩm
Route::get('/products', [ProductController::class, 'index'])
     ->name('products.index');

     Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])
     ->name('categories.show');
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

// Chỉ cho phép user đã đăng nhập truy cập Profile
Route::middleware('auth')->group(function () {
     // Show form sửa profile
     Route::get('/profile', [ProfileController::class, 'edit'])
          ->name('profile.edit');
 
     // Xử lý submit form update profile
     Route::put('/profile', [ProfileController::class, 'update'])
          ->name('profile.update');
 });
 Route::prefix('admin')->name('admin.')->group(function () {
     Route::get('menu',              [MenuController::class,'index'])->name('menu.index');
 
     /* Section */
     Route::post('menu/section',             [MenuController::class,'storeSection'])->name('menu.section.store');
     Route::put ('menu/section/{section}',   [MenuController::class,'updateSection'])->name('menu.section.update');
     Route::delete('menu/section/{section}', [MenuController::class,'destroySection'])->name('menu.section.destroy');
 
     /* Item */
     Route::post('menu/section/{section}/item',          [MenuController::class,'storeItem'])->name('menu.item.store');
     Route::put ('menu/item/{item}',                     [MenuController::class,'updateItem'])->name('menu.item.update');
     Route::delete('menu/item/{item}',                   [MenuController::class,'destroyItem'])->name('menu.item.destroy');
 });
require __DIR__ . '/auth.php';
