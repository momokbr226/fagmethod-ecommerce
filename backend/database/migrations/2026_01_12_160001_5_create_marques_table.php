<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marques', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('site_web')->nullable();
            $table->string('pays_origine')->nullable();
            $table->boolean('est_active')->default(true);
            $table->integer('ordre')->default(0);
            $table->json('meta_donnees')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('est_active');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marques');
    }
};
