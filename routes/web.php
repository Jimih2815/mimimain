<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Test header
Route::get('/header-demo', fn() => view('header-demo'))->name('header.demo');

// Danh sách sản phẩm
Route::get('/products', [ProductController::class, 'index'])
     ->name('products.index');

// Trang chủ
Route::get('/', [HomeController::class, 'index'])
     ->name('home');
use App\Http\Controllers\SearchController;

Route::get('/search', [SearchController::class, 'index'])
          ->name('search');

Route::get('/product/{id}', [ProductController::class, 'show'])
               ->name('product.show');