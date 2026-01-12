<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProduitController;
use App\Http\Controllers\Api\CategorieController;
use App\Http\Controllers\Api\PanierController;
use App\Http\Controllers\Api\CommandeController;

Route::prefix('v1')->group(function () {
    // Public routes - ORDER MATTERS!
    // Specific routes first
    Route::get('/products/featured', [ProduitController::class, 'featured']);
    Route::get('/products/search', [ProduitController::class, 'search']);
    Route::get('/products/category/{category}', [ProduitController::class, 'byCategory']);
    
    // General routes with parameters last
    Route::get('/products', [ProduitController::class, 'index']);
    Route::get('/products/{product}', [ProduitController::class, 'show']);
    
    // French routes for products
    Route::get('/produits/featured', [ProduitController::class, 'featured']);
    Route::get('/produits/search', [ProduitController::class, 'search']);
    Route::get('/produits', [ProduitController::class, 'index']);
    Route::get('/produits/{product}', [ProduitController::class, 'show']);
    
    Route::get('/categories', [CategorieController::class, 'index']);
    Route::get('/categories/tree', [CategorieController::class, 'index']);
    Route::get('/categories/with-products', [CategorieController::class, 'index']);
    Route::get('/categories/{category}', [CategorieController::class, 'show']);

    // Authentication routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/profile', [AuthController::class, 'profile']);
        Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
        Route::put('/auth/change-password', [AuthController::class, 'changePassword']);

        // Cart routes (French)
        Route::get('/paniers', [PanierController::class, 'index']);
        Route::post('/paniers/ajouter', [PanierController::class, 'addToCart']);
        Route::put('/paniers/{id}', [PanierController::class, 'updateCartItem']);
        Route::delete('/paniers/{id}', [PanierController::class, 'removeFromCart']);
        Route::delete('/paniers/vider', [PanierController::class, 'clearCart']);
        Route::get('/paniers/count', [PanierController::class, 'getCartCount']);

        // Order routes (French)
        Route::get('/commandes', [CommandeController::class, 'index']);
        Route::post('/commandes', [CommandeController::class, 'store']);
        Route::get('/commandes/{id}', [CommandeController::class, 'show']);
        Route::put('/commandes/{id}/statut', [CommandeController::class, 'updateStatus']);
    });
});
