<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1.  IMPORT CONTROLLERS
|--------------------------------------------------------------------------
| Chỗ này mình gom gọn theo nhóm cho dễ thấy.
*/

/** PUBLIC **/
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

/** AUTH **/
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;

/** ADMIN **/
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
| 2.  PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

/* 2.1. Auth (guest only) -------------------------------------------------- */
Route::middleware('guest')->group(function () {
    Route::get ('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);

    Route::get ('login',    [LoginController::class, 'showLoginForm'])       ->name('login');
    Route::post('login',    [LoginController::class, 'login']);
});

/* 2.2. Home + catalog ----------------------------------------------------- */
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products',        [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show']) ->name('products.show');

Route::get('/collections/{slug}',          [CollectionController::class,'show'])->name('collections.show');
Route::get('/categories',                  [CategoryController::class,'index'])->name('categories.index');
Route::get('/categories/{category:slug}',  [CategoryController::class,'show'])->name('categories.show');

/* 2.3. Cart & Checkout ---------------------------------------------------- */
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get  ('/',          [CartController::class,'index'])  ->name('index');
    Route::post ('add/{id}',   [CartController::class,'add'])    ->name('add');
    Route::post ('remove/{k}', [CartController::class,'remove']) ->name('remove');
});

Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get ('/',                 [CheckoutController::class,'show'])       ->name('show');
    Route::post('bank-ref',          [CheckoutController::class,'ajaxBankRef'])->name('bankRef');
    Route::post('confirm',           [CheckoutController::class,'confirm'])    ->name('confirm');
    Route::get ('success/{order}',   [CheckoutController::class,'success'])    ->name('success');
});

/* 2.4. Logout (mọi trạng thái) ------------------------------------------- */
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

/* 2.5. Profile (auth only) ------------------------------------------------ */
Route::middleware('auth')->group(function () {
    Route::get ('/profile', [ProfileController::class,'edit'])   ->name('profile.edit');
    Route::put('/profile',  [ProfileController::class,'update']) ->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| 3.  ADMIN ROUTES
|--------------------------------------------------------------------------
|   Prefix      : /admin
|   Name prefix : admin.
|   Middleware  : auth (bật lên khi xong).
*/
Route::prefix('admin')
     // ->middleware('auth')  // (Bật khi muốn yêu cầu đăng nhập)
     ->name('admin.')
     ->group(function () {

    /* ---------- 2.1. Mega-menu ---------- */
    Route::controller(MenuController::class)->group(function () {
        Route::get   ('menu',                         'index'           )->name('menu.index');
        Route::post  ('menu/section',                 'storeSection'    )->name('menu.section.store');
        Route::put   ('menu/section/{section}',       'updateSection'   )->name('menu.section.update');
        Route::delete('menu/section/{section}',       'destroySection'  )->name('menu.section.destroy');

        Route::post  ('menu/section/{section}/group', 'storeGroup'      )->name('menu.group.store');
        Route::put   ('menu/group/{group}',           'updateGroup'     )->name('menu.group.update');
        Route::delete('menu/group/{group}',           'destroyGroup'    )->name('menu.group.destroy');

        Route::post  ('menu/group/{group}/product',                     'addProductToGroup')
                                                                     ->name('menu.group.product.add');
        Route::delete('menu/group/{group}/product/{pid}',              'removeProductFromGroup')
                                                                     ->name('menu.group.product.remove');
    });

    /* ---------- 2.2. Orders ---------- */
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');

    /* ---------- 2.3. Products CRUD ---------- */
    Route::resource('products', AdminProductController::class);

    /* ---------- 2.4. Categories + Nhóm dropdown ---------- */
    Route::resource('categories', AdminCategoryController::class)->except('show');

    // CRUD group trong category
    Route::post   ('categories/{category}/groups',           [CategoryGroupController::class, 'store'])
         ->name  ('categories.groups.store');
    Route::put    ('categories/groups/{group}',              [CategoryGroupController::class, 'update'])
         ->name  ('categories.groups.update');
    Route::delete ('categories/groups/{group}',              [CategoryGroupController::class, 'destroy'])
         ->name  ('categories.groups.destroy');

    // Thêm / xoá sản phẩm vào group
    Route::post   ('categories/groups/{group}/product',      [CategoryGroupController::class, 'attachProduct'])
         ->name  ('categories.groups.product.attach');
    Route::delete ('categories/groups/{group}/product/{id}', [CategoryGroupController::class, 'detachProduct'])
         ->name  ('categories.groups.product.detach');

    /* ---------- 2.5. Users ---------- */
    Route::controller(AdminUserController::class)->group(function () {
        Route::get   ('users',                 'index'         )->name('users.index');
        Route::post  ('users/{user}/reset-password', 'resetPassword')
                                                       ->name('users.resetPassword');
        Route::get   ('users/{user}',          'show'          )->name('users.show');
        Route::delete('users/{user}',          'destroy'       )->name('users.destroy');
    });

    /* ---------- 2.6. Collections & Widgets ---------- */
    Route::resource('collections',       AdminCollectionController::class);
    Route::resource('widgets',           WidgetController::class);
    Route::resource('placements',        WidgetPlacementController::class);

    /* ---------- 2.7. Home-banner, Section Images, Sliders ---------- */
    Route::get ('home-banner',           [HomePageController::class, 'edit']  )->name('home.edit');
    Route::post('home-banner',           [HomePageController::class, 'update'])->name('home.update');

    Route::resource('collection-sliders', CollectionSliderController::class)->except('show');
    Route::post('collection-sliders/{collectionSlider}/move',
                [CollectionSliderController::class, 'move'])
         ->name('collection-sliders.move');

    Route::get  ('home-section-images',  [HomeSectionImageController::class, 'edit']  )->name('home.images.edit');
    Route::post ('home-section-images',  [HomeSectionImageController::class, 'update'])->name('home.images.update');

    Route::resource('product-sliders',   ProductSliderController::class)
         ->only(['index','create','store','edit','update','destroy']);
});

/* ------------------------------------------------------------------------
 | 3.  Redirect khi URL thừa dấu “/”
 *------------------------------------------------------------------------*/
Route::get('{any}/', function () {
    return redirect(rtrim(request()->getRequestUri(), '/'));
})->where('any', '.*');   // luôn CUỐI FILE