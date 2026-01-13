<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProduitController;
use App\Http\Controllers\Api\CategorieController;
use App\Http\Controllers\Api\PanierController;
use App\Http\Controllers\Api\CommandeController;
use App\Http\Controllers\Api\MarqueController;
use App\Http\Controllers\Api\FamilleProduitController;
use App\Http\Controllers\Api\FournisseurController;
use App\Http\Controllers\Api\ParametreController;

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
        Route::post('/paniers/code-promo', [PanierController::class, 'appliquerCodePromo']);
        Route::delete('/paniers/code-promo', [PanierController::class, 'retirerCodePromo']);

        // Order routes (French)
        Route::get('/commandes', [CommandeController::class, 'index']);
        Route::post('/commandes', [CommandeController::class, 'store']);
        Route::get('/commandes/{id}', [CommandeController::class, 'show']);
        Route::put('/commandes/{id}/statut', [CommandeController::class, 'updateStatus']);

        // Référentiels métier - Accessible aux admins et fournisseurs
        Route::middleware(['role:admin,fournisseur'])->group(function () {
            // Marques
            Route::get('/marques', [MarqueController::class, 'index']);
            Route::get('/marques/{id}', [MarqueController::class, 'show']);
            Route::post('/marques', [MarqueController::class, 'store'])->middleware('permission:creer-produit');
            Route::put('/marques/{id}', [MarqueController::class, 'update'])->middleware('permission:modifier-produit');
            Route::delete('/marques/{id}', [MarqueController::class, 'destroy'])->middleware('permission:supprimer-produit');

            // Familles de produits
            Route::get('/familles-produits', [FamilleProduitController::class, 'index']);
            Route::get('/familles-produits/{id}', [FamilleProduitController::class, 'show']);
            Route::post('/familles-produits', [FamilleProduitController::class, 'store'])->middleware('permission:creer-produit');
            Route::put('/familles-produits/{id}', [FamilleProduitController::class, 'update'])->middleware('permission:modifier-produit');
            Route::delete('/familles-produits/{id}', [FamilleProduitController::class, 'destroy'])->middleware('permission:supprimer-produit');

            // Fournisseurs - Admin uniquement
            Route::middleware(['role:admin'])->group(function () {
                Route::get('/fournisseurs', [FournisseurController::class, 'index']);
                Route::get('/fournisseurs/{id}', [FournisseurController::class, 'show']);
                Route::post('/fournisseurs', [FournisseurController::class, 'store']);
                Route::put('/fournisseurs/{id}', [FournisseurController::class, 'update']);
                Route::delete('/fournisseurs/{id}', [FournisseurController::class, 'destroy']);
            });
        });

        // Paramètres - Admin uniquement
        Route::middleware(['role:admin'])->prefix('parametres')->group(function () {
            Route::get('/', [ParametreController::class, 'index']);
            Route::get('/groupes', [ParametreController::class, 'groupes']);
            Route::get('/groupe/{groupe}', [ParametreController::class, 'byGroupe']);
            Route::get('/valeur/{cle}', [ParametreController::class, 'getValue']);
            Route::post('/', [ParametreController::class, 'store']);
            Route::put('/{id}', [ParametreController::class, 'update']);
            Route::put('/valeur/{cle}', [ParametreController::class, 'setValue']);
            Route::delete('/{id}', [ParametreController::class, 'destroy']);
        });
    });
});
