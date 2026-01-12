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
        Schema::create('parametres', function (Blueprint $table) {
            $table->id();
            $table->string('cle_parametre')->unique();
            $table->text('valeur_parametre');
            $table->text('description')->nullable();
            $table->enum('type_parametre', ['texte', 'nombre', 'booleen', 'json'])->default('texte');
            $table->string('groupe_parametre')->default('general');
            $table->boolean('est_public')->default(false);
            $table->boolean('est_modifiable')->default(true);
            $table->foreignId('utilisateur_id')->nullable()->constrained('utilisateurs')->onDelete('set null');
            $table->timestamps();
            
            $table->comment('Table des parametres de configuration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametres');
    }
};
