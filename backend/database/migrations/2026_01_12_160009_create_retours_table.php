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
        Schema::create('retours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('commandes')->onDelete('cascade');
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->foreignId('article_commande_id')->constrained('articles_commande')->onDelete('cascade');
            $table->enum('type_retour', ['remboursement', 'echange'])->default('remboursement');
            $table->enum('motif_retour', ['produit_defectueux', 'mauvaise_taille', 'ne_convient_pas', 'erreur_commande', 'autre'])->default('autre');
            $table->text('description_motif')->nullable();
            $table->enum('statut_retour', ['en_attente', 'accepte', 'refuse', 'en_cours', 'traite'])->default('en_attente');
            $table->decimal('montant_remboursement', 10, 2)->nullable();
            $table->string('reference_remboursement')->nullable();
            $table->json('images_retour')->nullable();
            $table->text('notes_admin')->nullable();
            $table->timestamp('date_demande')->nullable();
            $table->timestamp('date_traitement')->nullable();
            $table->timestamp('date_remboursement')->nullable();
            $table->timestamps();
            
            $table->comment('Table des retours de produits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retours');
    }
};
