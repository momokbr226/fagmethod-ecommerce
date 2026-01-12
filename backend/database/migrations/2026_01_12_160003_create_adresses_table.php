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
        Schema::create('adresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->enum('type', ['livraison', 'facturation']);
            $table->string('prenom');
            $table->string('nom');
            $table->string('entreprise')->nullable();
            $table->string('adresse_ligne_1');
            $table->string('adresse_ligne_2')->nullable();
            $table->string('ville');
            $table->string('code_postal');
            $table->string('pays')->default('France');
            $table->string('telephone')->nullable();
            $table->string('instructions_livraison')->nullable();
            $table->boolean('est_par_defaut')->default(false);
            $table->timestamps();
            
            $table->comment('Table des adresses de livraison et facturation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adresses');
    }
};
