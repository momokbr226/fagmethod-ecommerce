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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_commande')->unique();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->decimal('montant_total', 10, 2);
            $table->decimal('sous_total', 10, 2);
            $table->decimal('montant_tva', 10, 2)->default(0);
            $table->decimal('frais_livraison', 10, 2)->default(0);
            $table->decimal('montant_remise', 10, 2)->default(0);
            $table->enum('statut', ['en_attente', 'en_preparation', 'expedie', 'livre', 'annule'])->default('en_attente');
            $table->enum('statut_paiement', ['en_attente', 'paye', 'echoue', 'rembourse'])->default('en_attente');
            $table->string('mode_paiement')->nullable();
            $table->string('reference_paiement')->nullable();
            $table->json('adresse_livraison');
            $table->json('adresse_facturation')->nullable();
            $table->text('notes_commande')->nullable();
            $table->text('notes_livraison')->nullable();
            $table->timestamp('date_expedition')->nullable();
            $table->timestamp('date_livraison')->nullable();
            $table->string('numero_suivi')->nullable();
            $table->string('transporteur')->nullable();
            $table->text('instructions_livraison')->nullable();
            $table->timestamps();
            
            $table->comment('Table des commandes clients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
