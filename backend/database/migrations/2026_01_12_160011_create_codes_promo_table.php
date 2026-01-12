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
        Schema::create('codes_promo', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('nom_promo');
            $table->text('description')->nullable();
            $table->enum('type_remise', ['pourcentage', 'montant_fixe'])->default('pourcentage');
            $table->decimal('valeur_remise', 10, 2);
            $table->decimal('montant_minimum_achat', 10, 2)->default(0);
            $table->decimal('remise_maximale', 10, 2)->nullable();
            $table->integer('utilisation_max')->nullable();
            $table->integer('utilisation_par_client_max')->default(1);
            $table->integer('utilisations_count')->default(0);
            $table->timestamp('date_debut')->nullable();
            $table->timestamp('date_fin')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->json('produits_applicables')->nullable();
            $table->json('categories_applicables')->nullable();
            $table->json('clients_applicables')->nullable();
            $table->text('conditions_utilisation')->nullable();
            $table->timestamps();
            
            $table->comment('Table des codes promotionnels');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codes_promo');
    }
};
