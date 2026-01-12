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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('commandes')->onDelete('cascade');
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->decimal('montant', 10, 2);
            $table->string('devise', 3)->default('EUR');
            $table->enum('methode_paiement', ['carte_credit', 'paypal', 'virement', 'cheque', 'espece'])->default('carte_credit');
            $table->enum('statut', ['en_attente', 'autorise', 'echoue', 'annule', 'rembourse'])->default('en_attente');
            $table->string('transaction_id')->unique();
            $table->string('reference_paiement')->nullable();
            $table->json('details_paiement')->nullable();
            $table->timestamp('date_paiement')->nullable();
            $table->timestamp('date_confirmation')->nullable();
            $table->text('motif_echec')->nullable();
            $table->decimal('frais_transaction', 10, 2)->default(0);
            $table->string('porteur_carte')->nullable();
            $table->string('type_carte')->nullable();
            $table->string('derniers_chiffres_carte')->nullable();
            $table->timestamps();
            
            $table->comment('Table des paiements des commandes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
