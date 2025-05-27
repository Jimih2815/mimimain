<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. IMPORT CONTROLLERS
|--------------------------------------------------------------------------
|
| Phân chia theo nhóm: PUBLIC, AUTH, ADMIN
*/

// PUBLIC CONTROLLERS
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Models\Product;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HelpRequestController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\Admin\NewsController as AdminNewsCtrl;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\SectorFrontController;

// AUTH CONTROLLERS
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// ADMIN CONTROLLERS
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
use App\Http\Controllers\Admin\SidebarItemController;
use App\Http\Controllers\Admin\HelpRequestController as AdminHelpRequestController;
use App\Http\Controllers\Admin\SectorController as AdminSectorController;



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

    // ← chèn route menu-mobile ngay trong đây
    Route::get('menu-mobile', [CartController::class, 'menuMobile'])
         ->name('menu-mobile');
});

Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get  ('/',               [CheckoutController::class,'show'])->name('show');
    Route::post ('bank-ref',        [CheckoutController::class,'ajaxBankRef'])->name('bankRef');
    Route::post ('confirm',         [CheckoutController::class,'confirm'])->name('confirm');
    Route::get  ('success/{order}', [CheckoutController::class,'success'])->name('success');
});

// Trang checkout hiển thị form thanh toán
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');

// Xử lý Buy Now: thêm sản phẩm vào session rồi chuyển ngay tới checkout
Route::post('/checkout/buy-now/{product}', [CheckoutController::class, 'buyNow'])
     ->name('checkout.buyNow');


// trang desktop favorites
Route::get('/favorites', [FavoriteController::class, 'index'])
     ->name('favorites.index');

Route::post('/favorites/toggle/{product}', [FavoriteController::class, 'toggle'])
     ->name('favorites.toggle');


Route::view('/help', 'help')->name('help');
Route::middleware('auth')->group(function () {
    Route::get('/help',  [HelpRequestController::class, 'create'])
         ->name('help.create');
    Route::post('/help', [HelpRequestController::class, 'store'])
         ->name('help.store');
});

Route::get('favorites', [FavoriteController::class,'index'])->name('favorites.index');
Route::post('favorites/toggle/{product}', [FavoriteController::class,'toggle'])->name('favorites.toggle');

Route::get('/news', [NewsController::class,'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class,'show'])->name('news.show');
Route::view('/about', 'about')->name('about');
Route::view('/vision-mission', 'vision-mission')->name('vision-mission');
Route::view('/commitment', 'commitment')->name('commitment');
Route::view('/policy/return', 'return-policy')->name('policy.return');
Route::view('/policy/warranty', 'warranty-policy')->name('policy.warranty');
Route::view('/policy/shipping', 'shipping-policy')->name('policy.shipping');
Route::view('/policy/privacy', 'privacy-policy')->name('policy.privacy');
Route::view('/faq', 'faq')->name('faq');
Route::view('/how-to-order', 'how-to-order')->name('how-to-order');
Route::view('/tracking', 'tracking')->name('tracking');
Route::view('/how-to-pay', 'how-to-pay')->name('how-to-pay');



Route::get('/sectors', [SectorController::class, 'index'])
      ->name('sector.index');

Route::get('/sector/{slug}', [SectorFrontController::class,'show'])
     ->name('sector.show');




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
    Route::get('profile', [ProfileController::class,'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class,'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])
     ->name('profile.password.update');
     Route::post('/profile/orders/{order}/cancel', [ProfileController::class, 'cancelOrder'])
         ->name('orders.cancel');

});
// Hiển thị form nhập email để gửi link
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
     ->name('password.request');

// Xử lý gửi email
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
     ->name('password.email');

// Hiển thị form nhập mật khẩu mới (sau khi click link trong email)
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
     ->name('password.reset');

// Xử lý lưu mật khẩu mới
Route::post('password/reset', [ResetPasswordController::class, 'reset'])
     ->name('password.update');

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
     ->only(['index','show','destroy','update']);

    // 4.4 Quản lý sản phẩm
    Route::resource('products', AdminProductController::class);
    // route để TinyMCE upload ảnh
    Route::post('products/upload-image', [AdminProductController::class, 'uploadImage'])
         ->name('products.uploadImage');

    // 4.6 Quản lý người dùng
    Route::controller(AdminUserController::class)->group(function () {
        Route::get   ('users',                       'index')->name('users.index');
        Route::get   ('users/{user}',                'show')->name('users.show');
        Route::post  ('users/{user}/reset-password', 'resetPassword')->name('users.resetPassword');
        Route::delete('users/{user}',                'destroy')->name('users.destroy');
    });

    // 4.7 Collections
    Route::resource('collections', AdminCollectionController::class);

    // Collection‐Sliders: reorder AJAX
    Route::post('collection-sliders/reorder', [CollectionSliderController::class, 'reorder'])
         ->name('collection-sliders.reorder');
    // Collection‐Sliders: move up/down
    Route::post('collection-sliders/{collection_slider}/move',
         [CollectionSliderController::class,'move'])
         ->name('collection-sliders.move');
    // Collection‐Sliders: CRUD (except show)
    Route::resource('collection-sliders', CollectionSliderController::class)
         ->except(['show']);

    // 4.8 Home Page & Home Section Images
    Route::get  ('home',                [HomePageController::class,'edit'])->name('home.edit');
    Route::post ('home',                [HomePageController::class,'update'])->name('home.update');
    Route::get  ('home-section-images', [HomeSectionImageController::class,'index'])->name('home-section-images.index');
    Route::post ('home-section-images', [HomeSectionImageController::class,'update'])->name('home-section-images.update');

    // 4.9 Product Sliders
    Route::resource('product-sliders', ProductSliderController::class)
         ->only(['index','create','store','edit','update','destroy']);
    Route::post('product-sliders/reorder', [ProductSliderController::class, 'reorder'])
         ->name('product-sliders.reorder');

    // 4.10 Widgets & Placements
    Route::resource('widgets',         WidgetController::class);
    Route::resource('placements',      WidgetPlacementController::class);
    Route::resource('sidebar-items',   SidebarItemController::class);

    Route::post(
     'help-requests/{helpRequest}/update',
     [AdminHelpRequestController::class, 'update']
     )->name('help_requests.update');
     // Admin (nằm ngoài auth nếu bạn có group admin riêng, hoặc để trong middleware('auth','is_admin'))
         // 4.x Quản lý News
     Route::resource('news', AdminNewsCtrl::class)
          ->except(['show']);
     // Tuỳ Im­age upload cho TinyMCE
     Route::post('news/upload-image', [AdminNewsCtrl::class, 'uploadImage'])
          ->name('news.uploadImage'); 
          
     // chọn collection global cho news/index
     Route::post('news/select-collection',
             [AdminNewsCtrl::class, 'selectCollection']
         )->name('news.selectCollection');

     // assign từng tin tức vào collection
     Route::post('news/{news}/assign-collection',
             [AdminNewsCtrl::class, 'assignCollection']
         )->name('news.assignCollection');

     Route::resource('sectors', AdminSectorController::class);
     Route::post('sectors/reorder', [AdminSectorController::class, 'reorder'])->name('sectors.reorder');

});


