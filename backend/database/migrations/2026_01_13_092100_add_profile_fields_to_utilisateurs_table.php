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
        Schema::table('utilisateurs', function (Blueprint $table) {
            // Type de profil : personne physique ou morale
            $table->enum('type_profil', ['physique', 'morale'])->default('physique')->after('nom_complet');
            
            // Informations pour personne morale
            $table->string('raison_sociale')->nullable()->after('type_profil');
            $table->string('siret')->nullable()->after('raison_sociale');
            $table->string('numero_tva')->nullable()->after('siret');
            $table->string('forme_juridique')->nullable()->after('numero_tva');
            
            // Informations supplémentaires
            $table->string('nom_contact')->nullable()->after('forme_juridique')->comment('Nom du contact pour personne morale');
            $table->string('prenom_contact')->nullable()->after('nom_contact')->comment('Prénom du contact pour personne morale');
            $table->string('fonction_contact')->nullable()->after('prenom_contact')->comment('Fonction du contact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->dropColumn([
                'type_profil',
                'raison_sociale',
                'siret',
                'numero_tva',
                'forme_juridique',
                'nom_contact',
                'prenom_contact',
                'fonction_contact'
            ]);
        });
    }
};
