<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
*/
Route::get('/listProducts', [ProductController::class, 'listProducts']);
Route::post('/products/add', [ProductController::class, 'addProduct']);
Route::post('/products/update', [ProductController::class, 'updateProduct']);
Route::post('/products/delete', [ProductController::class, 'deleteProduct']);

/*
|--------------------------------------------------------------------------
| Cart Routes (GET & POST Only)
|--------------------------------------------------------------------------
*/
Route::post('/cart/add', [CartController::class, 'addToCart']);
Route::get('/cart/view', [CartController::class, 'viewCart']);

Route::post('/cart/update', [CartController::class, 'updateCart']);
Route::post('/cart/remove', [CartController::class, 'removeFromCart']);
Route::post('/cart/clear', [CartController::class, 'clearCart']);
