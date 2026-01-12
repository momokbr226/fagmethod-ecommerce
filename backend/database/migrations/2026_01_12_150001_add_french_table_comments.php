<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ajouter des commentaires français aux tables
        DB::statement("ALTER TABLE `users` COMMENT = 'Table des utilisateurs du site'");
        
        DB::statement("ALTER TABLE `categories` COMMENT = 'Table des catégories de produits'");
        
        DB::statement("ALTER TABLE `products` COMMENT = 'Table des produits du catalogue'");
        
        DB::statement("ALTER TABLE `orders` COMMENT = 'Table des commandes clients'");
        
        DB::statement("ALTER TABLE `order_items` COMMENT = 'Table des articles de commandes'");
        
        DB::statement("ALTER TABLE `carts` COMMENT = 'Table des paniers d\\'achat'");
        
        DB::statement("ALTER TABLE `cart_items` COMMENT = 'Table des articles dans les paniers'");
        
        DB::statement("ALTER TABLE `addresses` COMMENT = 'Table des adresses de livraison'");
        
        DB::statement("ALTER TABLE `failed_jobs` COMMENT = 'Table des tâches échouées'");
        
        DB::statement("ALTER TABLE `cache` COMMENT = 'Table de cache du système'");
        
        DB::statement("ALTER TABLE `sessions` COMMENT = 'Table des sessions utilisateur'");
        
        DB::statement("ALTER TABLE `personal_access_tokens` COMMENT = 'Table des jetons d\\'accès personnel'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer les commentaires des tables
        DB::statement("ALTER TABLE `users` COMMENT = ''");
        DB::statement("ALTER TABLE `categories` COMMENT = ''");
        DB::statement("ALTER TABLE `products` COMMENT = ''");
        DB::statement("ALTER TABLE `orders` COMMENT = ''");
        DB::statement("ALTER TABLE `order_items` COMMENT = ''");
        DB::statement("ALTER TABLE `carts` COMMENT = ''");
        DB::statement("ALTER TABLE `cart_items` COMMENT = ''");
        DB::statement("ALTER TABLE `addresses` COMMENT = ''");
        DB::statement("ALTER TABLE `failed_jobs` COMMENT = ''");
        DB::statement("ALTER TABLE `cache` COMMENT = ''");
        DB::statement("ALTER TABLE `sessions` COMMENT = ''");
        DB::statement("ALTER TABLE `personal_access_tokens` COMMENT = ''");
    }
};
