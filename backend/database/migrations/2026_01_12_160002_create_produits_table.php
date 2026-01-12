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
            $table->string('nom');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('description_courte')->nullable();
            $table->string('reference')->unique();
            $table->decimal('prix', 10, 2);
            $table->decimal('prix_compare', 10, 2)->nullable();
            $table->integer('quantite_stock')->default(0);
            $table->boolean('suivi_quantite')->default(true);
            $table->boolean('est_actif')->default(true);
            $table->boolean('est_en_rupture')->default(false);
            $table->boolean('est_mise_en_avant')->default(false);
            $table->string('image_principale')->nullable();
            $table->json('images_supplementaires')->nullable();
            $table->decimal('poids', 8, 2)->nullable();
            $table->decimal('dimensions_longueur', 8, 2)->nullable();
            $table->decimal('dimensions_largeur', 8, 2)->nullable();
            $table->decimal('dimensions_hauteur', 8, 2)->nullable();
            $table->json('attributs')->nullable();
            $table->text('specifications_techniques')->nullable();
            $table->foreignId('categorie_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->text('meta_description')->nullable();
            $table->text('meta_titre')->nullable();
            $table->string('marque')->nullable();
            $table->string('modele')->nullable();
            $table->text('garantie')->nullable();
            $table->timestamps();
            
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
