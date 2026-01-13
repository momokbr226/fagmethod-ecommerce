<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->string('raison_sociale');
            $table->string('siret', 14)->unique()->nullable();
            $table->string('numero_tva', 20)->nullable();
            $table->string('code_fournisseur')->unique();
            
            // Informations de contact
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('fax')->nullable();
            $table->string('site_web')->nullable();
            
            // Adresse
            $table->text('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('pays')->default('France');
            
            // Contact principal
            $table->string('nom_contact')->nullable();
            $table->string('prenom_contact')->nullable();
            $table->string('fonction_contact')->nullable();
            $table->string('email_contact')->nullable();
            $table->string('telephone_contact')->nullable();
            
            // Informations commerciales
            $table->decimal('delai_livraison_moyen', 5, 2)->nullable()->comment('En jours');
            $table->decimal('montant_minimum_commande', 10, 2)->nullable();
            $table->enum('conditions_paiement', ['comptant', '30_jours', '60_jours', '90_jours'])->default('30_jours');
            $table->text('notes')->nullable();
            
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('est_actif');
            $table->index('code_fournisseur');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fournisseurs');
    }
};
