<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;

// 1) Trang chủ: trả về view resources/views/home.blade.php
Route::view('/', 'home')->name('home');

// 2) Danh sách & Search sản phẩm
Route::get('/products', [ProductController::class, 'index'])
     ->name('products.index');

// 3) Chi tiết sản phẩm theo slug
Route::get('/products/{slug}', [ProductController::class, 'show'])
     ->name('products.show');

// 4) Giỏ hàng
Route::get('/cart',    [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}',    [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
