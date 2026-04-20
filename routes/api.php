<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'listProducts']);
    Route::post('/add', [ProductController::class, 'addProduct']);
    Route::post('/update', [ProductController::class, 'updateProduct']);
    Route::post('/delete', [ProductController::class, 'deleteProduct']);
});

Route::prefix('cart')->group(function () {
    Route::post('/add', [CartController::class, 'addToCart']);
    Route::get('/view', [CartController::class, 'viewCart']);
    Route::post('/update', [CartController::class, 'updateCart']);
    Route::post('/remove', [CartController::class, 'removeFromCart']);
    Route::post('/clear', [CartController::class, 'clearCart']);
});
