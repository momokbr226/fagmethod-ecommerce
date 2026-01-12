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
        Schema::create('faq', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('reponse');
            $table->string('categorie_faq')->nullable();
            $table->integer('ordre_affichage')->default(0);
            $table->boolean('est_publie')->default(true);
            $table->integer('nombre_vues')->default(0);
            $table->boolean('est_utile')->default(false);
            $table->integer('nombre_votes_utiles')->default(0);
            $table->json('tags')->nullable();
            $table->foreignId('utilisateur_id')->nullable()->constrained('utilisateurs')->onDelete('set null');
            $table->timestamps();
            
            $table->comment('Table des questions fréquemment posées');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faq');
    }
};
