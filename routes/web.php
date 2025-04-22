<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Tìm kiếm
Route::get('/search', [SearchController::class, 'index'])->name('search');

// --- Categories flow ---
// 1) Danh mục gốc
Route::get('/categories', [CategoryController::class, 'index'])
     ->name('categories.index');
// 2) Category → nếu có children: show children; else: show products
Route::get('/categories/{id}', [CategoryController::class, 'show'])
     ->name('categories.show');

// --- Products flow ---
// 3) TẤT CẢ SẢN PHẨM
Route::get('/products', [ProductController::class, 'index'])
     ->name('products.index');
// 4) Product Detail (chọn màu variants)
Route::get('/product/{id}', [ProductController::class, 'showProduct'])
     ->name('product.show');
// 5) Classification Detail (chọn size)
Route::get('/products/{id}', [ProductController::class, 'show'])
     ->name('products.show');
// Trang hiển thị giỏ hàng
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

// Thêm sản phẩm vào giỏ
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');

// Xóa sản phẩm khỏi giỏ (tuỳ chọn)
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');