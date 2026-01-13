<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            
            // Informations de base
            $table->string('nom');
            $table->string('slug')->unique();
            $table->string('reference')->unique()->comment('SKU ou référence produit');
            $table->text('description')->nullable();
            $table->text('description_courte')->nullable();
            
            // Relations avec référentiels
            $table->foreignId('categorie_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('marque_id')->nullable()->constrained('marques')->onDelete('set null');
            $table->foreignId('famille_id')->nullable()->constrained('familles_produits')->onDelete('set null');
            $table->foreignId('fournisseur_id')->nullable()->constrained('fournisseurs')->onDelete('set null');
            
            // Prix
            $table->decimal('prix', 10, 2);
            $table->decimal('prix_promo', 10, 2)->nullable();
            $table->date('date_debut_promo')->nullable();
            $table->date('date_fin_promo')->nullable();
            $table->decimal('prix_achat', 10, 2)->nullable()->comment('Prix d\'achat fournisseur');
            
            // Stock
            $table->integer('quantite_stock')->default(0);
            $table->integer('seuil_alerte_stock')->default(10);
            $table->enum('statut_stock', ['en_stock', 'stock_faible', 'rupture', 'sur_commande'])->default('en_stock');
            $table->boolean('gestion_stock')->default(true)->comment('Active/désactive la gestion du stock');
            
            // Caractéristiques physiques
            $table->decimal('poids', 8, 2)->nullable()->comment('Poids en kg');
            $table->decimal('longueur', 8, 2)->nullable()->comment('Longueur en cm');
            $table->decimal('largeur', 8, 2)->nullable()->comment('Largeur en cm');
            $table->decimal('hauteur', 8, 2)->nullable()->comment('Hauteur en cm');
            
            // Médias
            $table->string('image_principale')->nullable();
            $table->json('images')->nullable()->comment('Galerie d\'images');
            
            // Visibilité et statut
            $table->boolean('est_visible')->default(true);
            $table->boolean('est_nouveau')->default(false);
            $table->boolean('est_vedette')->default(false);
            $table->boolean('est_promo')->default(false);
            $table->integer('ordre')->default(0);
            
            // SEO et métadonnées
            $table->string('meta_titre')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('meta_donnees')->nullable();
            
            // Caractéristiques techniques
            $table->json('caracteristiques')->nullable()->comment('Attributs techniques du produit');
            
            // Statistiques
            $table->integer('vues')->default(0);
            $table->integer('ventes')->default(0);
            $table->decimal('note_moyenne', 3, 2)->nullable();
            $table->integer('nombre_avis')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour optimisation
            $table->index(['categorie_id', 'est_visible']);
            $table->index(['marque_id', 'est_visible']);
            $table->index(['famille_id', 'est_visible']);
            $table->index('slug');
            $table->index('reference');
            $table->index(['est_vedette', 'est_visible']);
            $table->index(['est_promo', 'est_visible']);
            
            $table->comment('Table des produits du catalogue');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
