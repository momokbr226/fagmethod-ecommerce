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
        Schema::create('avis_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->foreignId('produit_id')->constrained('produits')->onDelete('cascade');
            $table->foreignId('commande_id')->nullable()->constrained('commandes')->onDelete('set null');
            $table->integer('note')->unsigned();
            $table->text('titre_avis')->nullable();
            $table->text('contenu_avis');
            $table->enum('statut_avis', ['en_attente', 'approuve', 'rejete'])->default('en_attente');
            $table->json('images_avis')->nullable();
            $table->boolean('est_achat_verifie')->default(false);
            $table->boolean('est_recommande')->default(false);
            $table->integer('nombre_votes_utiles')->default(0);
            $table->text('reponse_vendeur')->nullable();
            $table->timestamp('date_reponse')->nullable();
            $table->timestamps();
            
            $table->comment('Table des avis clients sur les produits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avis_clients');
    }
};
