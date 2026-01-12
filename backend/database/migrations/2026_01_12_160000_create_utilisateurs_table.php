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
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom_complet');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('mot_de_passe');
            $table->string('telephone')->nullable();
            $table->date('date_naissance')->nullable();
            $table->enum('sexe', ['homme', 'femme', 'autre'])->nullable();
            $table->text('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('pays')->default('France');
            $table->boolean('est_actif')->default(true);
            $table->string('photo_profil')->nullable();
            $table->json('preferences')->nullable();
            $table->text('notes')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            $table->comment('Table des utilisateurs du site');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};
