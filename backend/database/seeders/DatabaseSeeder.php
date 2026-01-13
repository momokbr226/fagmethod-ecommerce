<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Démarrage du seeding de la base de données...');
        $this->command->newLine();

        // 1. Rôles et Permissions (OBLIGATOIRE EN PREMIER)
        $this->command->info('📋 Étape 1/5 : Rôles et Permissions');
        $this->call(RolePermissionSeeder::class);
        $this->command->newLine();

        // 2. Référentiels métier
        $this->command->info('📦 Étape 2/5 : Référentiels (Marques, Familles, Fournisseurs)');
        $this->call(ReferentielsSeeder::class);
        $this->command->newLine();

        // 3. Catégories
        $this->command->info('🏷️  Étape 3/5 : Catégories');
        $this->call(CategorieSeeder::class);
        $this->command->newLine();

        // 4. Utilisateurs de test
        $this->command->info('👥 Étape 4/5 : Utilisateurs de test');
        $this->call(UtilisateursTestSeeder::class);
        $this->command->newLine();

        // 5. Produits de test
        $this->command->info('🛍️  Étape 5/5 : Produits de test');
        $this->call(ProduitsTestSeeder::class);
        $this->command->newLine();

        $this->command->info('✅ Seeding terminé avec succès!');
        $this->command->newLine();
        $this->command->info('📧 Comptes de test créés:');
        $this->command->info('   Admin:       admin@fagmethod.com / password');
        $this->command->info('   Client:      jean.dupont@example.com / password');
        $this->command->info('   Fournisseur: contact@techdistrib.fr / password');
    }
}
