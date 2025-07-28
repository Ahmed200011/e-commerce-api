<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Dashboard\BannerController;
use App\Http\Controllers\Api\Dashboard\CategoryController;
use App\Http\Controllers\Api\ecommerce\CartController;
use App\Http\Controllers\Api\ecommerce\ContactController;
use App\Http\Controllers\Api\ecommerce\HomeController;
use App\Http\Controllers\Api\Dashboard\ProductController;
use App\Http\Controllers\Api\Dashboard\UserController;
use App\Http\Controllers\Api\ecommerce\OrderController;
use App\Http\Controllers\Api\ecommerce\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
});
Route::prefix('dashboard')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/categories', CategoryController::class);
    Route::apiResource('/products', ProductController::class);
    Route::apiResource('/banner', BannerController::class);
});

Route::prefix('e_commerce')->group(function () {
    Route::controller(HomeController::class)->group(function () {
        Route::get('/home', 'index');
        Route::get('/banner/{id}', 'bannerShowDetails');
        Route::get('/product/{id}', 'productShowDetails');
        Route::get('/category/{id}', 'categoryShowDetails');
    });

    Route::post('/contact_us', [ContactController::class, 'contactUs']);
    Route::get('/products/search', [ShopController::class, 'search']);

    Route::prefix('cart')
        ->middleware('auth:sanctum')
        ->controller(CartController::class)
        ->group(function () {
            Route::post('/add', 'addToCart');
            Route::get('/', 'viewCart');
            Route::put('/update/{product_id}', 'updateQuantity');
            Route::delete('/remove/{product_id}', 'removeFromCart');
            Route::delete('/clear', 'clearCart');
        });
    Route::prefix('order')
        ->middleware('auth:sanctum')
        ->controller(OrderController::class)
        ->group(function () {
            Route::post('/checkout', 'checkout');
            Route::get('/my-orders', 'myOrders');
            Route::post('/pay/{orderId}', 'markAsPaid');
            Route::delete('/cancel/{orderId}', 'OrderCancel');
        });
});
