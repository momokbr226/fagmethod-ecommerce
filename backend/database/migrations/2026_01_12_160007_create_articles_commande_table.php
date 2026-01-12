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
        Schema::create('articles_commande', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('commandes')->onDelete('cascade');
            $table->foreignId('produit_id')->constrained('produits')->onDelete('cascade');
            $table->string('nom_produit');
            $table->string('reference_produit');
            $table->decimal('prix_unitaire', 10, 2);
            $table->integer('quantite');
            $table->decimal('total_ligne', 10, 2);
            $table->decimal('remise_ligne', 10, 2)->default(0);
            $table->json('attributs_produit')->nullable();
            $table->text('notes_article')->nullable();
            $table->string('etat_retour')->nullable();
            $table->timestamp('date_retour')->nullable();
            $table->text('motif_retour')->nullable();
            $table->timestamps();
            
            $table->comment('Table des articles de commandes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles_commande');
    }
};
