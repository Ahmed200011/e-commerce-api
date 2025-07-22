<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Dashboard\BannerController;
use App\Http\Controllers\Api\Dashboard\CategoryController;
use App\Http\Controllers\Api\ecommerce\HomeController;
use App\Http\Controllers\Api\Dashboard\ProductController;
use App\Http\Controllers\Api\Dashboard\UserController;
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

Route::prefix('e-commerce')->group(function () {
    Route::get('/home', [ HomeController::class,'index']);
    Route::post('/contact_us', [ HomeController::class,'contactUs'])->middleware('auth:sanctum');

});


