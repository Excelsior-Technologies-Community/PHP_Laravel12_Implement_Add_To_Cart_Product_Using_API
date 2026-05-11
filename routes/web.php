<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;

Route::get('/products-ui', function () {
    return view('products');
})->name('products.ui');

Route::get('/cart-ui', function () {
    return view('cart');
})->name('cart.ui');