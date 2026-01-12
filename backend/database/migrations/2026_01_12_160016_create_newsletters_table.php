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
        Schema::create('newsletters', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('nom_complet')->nullable();
            $table->enum('statut_abonnement', ['actif', 'desinscrit', 'en_attente'])->default('en_attente');
            $table->string('token_desinscription')->unique();
            $table->timestamp('date_inscription')->nullable();
            $table->timestamp('date_desinscription')->nullable();
            $table->timestamp('date_confirmation')->nullable();
            $table->string('ip_inscription')->nullable();
            $table->string('pays')->nullable();
            $table->string('langue')->default('fr');
            $table->json('preferences')->nullable();
            $table->integer('nombre_emails_envoyes')->default(0);
            $table->integer('nombre_ouvertures')->default(0);
            $table->integer('nombre_clics')->default(0);
            $table->timestamp('derniere_ouverture')->nullable();
            $table->timestamp('dernier_clic')->nullable();
            $table->timestamps();
            
            $table->comment('Table des abonnés à la newsletter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletters');
    }
};
