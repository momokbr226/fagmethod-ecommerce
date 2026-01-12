<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;

Route::get('/', function () {
    return view('welcome');
});

// API routes accessible from web - ORDER MATTERS!
Route::prefix('api/v1')->group(function () {
    // Specific routes first
    Route::get('/products/featured', [ProductController::class, 'featured']);
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::get('/products/category/{category}', [ProductController::class, 'byCategory']);
    
    // General routes with parameters last
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    
    // Category routes
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/tree', [CategoryController::class, 'tree']);
    Route::get('/categories/with-products', [CategoryController::class, 'withProducts']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
});
