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
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->foreignId('produit_id')->constrained('produits')->onDelete('cascade');
            $table->string('nom_liste')->default('Ma liste de souhaits');
            $table->text('notes')->nullable();
            $table->boolean('est_public')->default(false);
            $table->timestamp('date_ajout')->nullable();
            $table->timestamps();
            
            $table->unique(['utilisateur_id', 'produit_id']);
            $table->comment('Table des listes de souhaits des utilisateurs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
