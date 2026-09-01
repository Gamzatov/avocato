<?php

use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/cities', [MenuController::class, 'cities']);
Route::get('/categories', [MenuController::class, 'categories']);
Route::get('/menu/{city:slug}', [MenuController::class, 'menu']);
Route::post('/orders', [OrderController::class, 'store']);
