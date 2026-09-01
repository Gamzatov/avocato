<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::redirect('/dashboard', '/admin/products')
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::redirect('/', '/admin/products')->name('dashboard');
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('products', ProductController::class)->except(['show']);
        Route::resource('orders', OrderController::class)->only(['index', 'show', 'update', 'destroy']);
    });

require __DIR__.'/auth.php';
