<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les permissions
        $permissions = [
            // Gestion des produits
            'voir-produits',
            'creer-produit',
            'modifier-produit',
            'supprimer-produit',
            
            // Gestion des catégories
            'voir-categories',
            'creer-categorie',
            'modifier-categorie',
            'supprimer-categorie',
            
            // Gestion des commandes
            'voir-commandes',
            'voir-toutes-commandes',
            'creer-commande',
            'modifier-commande',
            'annuler-commande',
            
            // Gestion des utilisateurs
            'voir-utilisateurs',
            'creer-utilisateur',
            'modifier-utilisateur',
            'supprimer-utilisateur',
            
            // Gestion du panier
            'gerer-panier',
            
            // Gestion des avis
            'creer-avis',
            'modifier-avis',
            'supprimer-avis',
            'moderer-avis',
            
            // Gestion des fournisseurs
            'gerer-stock',
            'voir-statistiques-fournisseur',
            
            // Administration
            'acceder-admin',
            'voir-statistiques',
            'gerer-parametres',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Créer les rôles et assigner les permissions

        // Rôle Admin - Tous les droits
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());

        // Rôle Client - Droits limités
        $clientRole = Role::create(['name' => 'client', 'guard_name' => 'web']);
        $clientRole->givePermissionTo([
            'voir-produits',
            'voir-categories',
            'voir-commandes',
            'creer-commande',
            'annuler-commande',
            'gerer-panier',
            'creer-avis',
            'modifier-avis',
        ]);

        // Rôle Fournisseur - Gestion des produits et stocks
        $fournisseurRole = Role::create(['name' => 'fournisseur', 'guard_name' => 'web']);
        $fournisseurRole->givePermissionTo([
            'voir-produits',
            'creer-produit',
            'modifier-produit',
            'voir-categories',
            'voir-commandes',
            'gerer-stock',
            'voir-statistiques-fournisseur',
        ]);

        $this->command->info('Rôles et permissions créés avec succès!');
        $this->command->info('- Admin: Tous les droits');
        $this->command->info('- Client: Achats et avis');
        $this->command->info('- Fournisseur: Gestion produits et stocks');
    }
}
