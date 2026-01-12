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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('nom_complet');
            $table->string('email');
            $table->string('telephone')->nullable();
            $table->enum('sujet_contact', ['information', 'support_technique', 'reclamation', 'suggestion', 'partenariat'])->default('information');
            $table->string('sujet_personnalise')->nullable();
            $table->text('message');
            $table->enum('statut_contact', ['nouveau', 'en_cours', 'traite', 'ferme'])->default('nouveau');
            $table->enum('priorite', ['basse', 'normale', 'haute', 'urgente'])->default('normale');
            $table->foreignId('utilisateur_id')->nullable()->constrained('utilisateurs')->onDelete('set null');
            $table->foreignId('assigne_a')->nullable()->constrained('utilisateurs')->onDelete('set null');
            $table->text('reponse')->nullable();
            $table->timestamp('date_reponse')->nullable();
            $table->integer('delai_reponse_heures')->nullable();
            $table->json('fichiers_joints')->nullable();
            $table->string('reference_ticket')->unique();
            $table->timestamps();
            
            $table->comment('Table des demandes de contact et support');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
