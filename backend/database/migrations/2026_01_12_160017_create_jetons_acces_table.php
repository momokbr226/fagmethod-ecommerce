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
        Schema::create('jetons_acces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->string('nom_jeton');
            $table->string('valeur_jeton')->unique();
            $table->text('permissions')->nullable();
            $table->timestamp('expire_le')->nullable();
            $table->timestamp('derniere_utilisation')->nullable();
            $table->string('adresse_ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
            
            $table->comment('Table des jetons d\'accès personnel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jetons_acces');
    }
};
