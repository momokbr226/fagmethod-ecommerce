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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->string('titre_notification');
            $table->text('contenu_notification');
            $table->enum('type_notification', ['commande', 'paiement', 'livraison', 'promotion', 'avis', 'systeme'])->default('systeme');
            $table->enum('canal_notification', ['email', 'sms', 'push', 'interne'])->default('email');
            $table->boolean('est_lue')->default(false);
            $table->timestamp('date_lecture')->nullable();
            $table->json('donnees_supplementaires')->nullable();
            $table->string('lien_action')->nullable();
            $table->timestamp('date_envoi')->nullable();
            $table->timestamp('date_expiration')->nullable();
            $table->timestamps();
            
            $table->comment('Table des notifications utilisateurs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
