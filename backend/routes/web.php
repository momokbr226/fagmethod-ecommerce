<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProduitController;
use App\Http\Controllers\Api\CategorieController;

Route::get('/', function () {
    return view('welcome');
});

// API routes accessible from web - ORDER MATTERS!
Route::prefix('api/v1')->group(function () {
    // Specific routes first
    Route::get('/products/featured', [ProduitController::class, 'featured']);
    Route::get('/products/search', [ProduitController::class, 'search']);
    Route::get('/products/category/{category}', [ProduitController::class, 'byCategory']);
    
    // General routes with parameters last
    Route::get('/products', [ProduitController::class, 'index']);
    Route::get('/products/{product}', [ProduitController::class, 'show']);
    
    // Category routes
    Route::get('/categories', [CategorieController::class, 'index']);
    Route::get('/categories/tree', [CategorieController::class, 'index']);
    Route::get('/categories/with-products', [CategorieController::class, 'index']);
    Route::get('/categories/{category}', [CategorieController::class, 'show']);
});
