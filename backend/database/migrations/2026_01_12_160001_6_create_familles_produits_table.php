<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('familles_produits', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('code')->unique()->comment('Code interne de la famille');
            $table->boolean('est_active')->default(true);
            $table->integer('ordre')->default(0);
            $table->json('attributs_specifiques')->nullable()->comment('Attributs spécifiques à cette famille');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('est_active');
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('familles_produits');
    }
};
