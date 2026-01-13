<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;

class UtilisateursTestSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un administrateur
        $admin = Utilisateur::create([
            'nom_complet' => 'Admin FAGMETHOD',
            'email' => 'admin@fagmethod.com',
            'mot_de_passe' => Hash::make('password'),
            'telephone' => '+33 1 23 45 67 89',
            'adresse' => '123 Avenue des Champs-Élysées',
            'ville' => 'Paris',
            'code_postal' => '75008',
            'pays' => 'France',
            'est_actif' => true,
            'type_profil' => 'physique',
        ]);
        $admin->assignRole('admin');

        // Créer plusieurs clients
        $clients = [
            [
                'nom_complet' => 'Jean Dupont',
                'email' => 'jean.dupont@example.com',
                'telephone' => '+33 6 12 34 56 78',
                'adresse' => '45 Rue de la République',
                'ville' => 'Lyon',
                'code_postal' => '69001',
                'type_profil' => 'physique',
            ],
            [
                'nom_complet' => 'Marie Martin',
                'email' => 'marie.martin@example.com',
                'telephone' => '+33 6 23 45 67 89',
                'adresse' => '78 Boulevard Victor Hugo',
                'ville' => 'Marseille',
                'code_postal' => '13001',
                'type_profil' => 'physique',
            ],
            [
                'nom_complet' => 'Pierre Bernard',
                'email' => 'pierre.bernard@example.com',
                'telephone' => '+33 6 34 56 78 90',
                'adresse' => '12 Place de la Comédie',
                'ville' => 'Bordeaux',
                'code_postal' => '33000',
                'type_profil' => 'physique',
            ],
            [
                'nom_complet' => 'Sophie Dubois',
                'email' => 'sophie.dubois@example.com',
                'telephone' => '+33 6 45 67 89 01',
                'adresse' => '89 Rue Nationale',
                'ville' => 'Lille',
                'code_postal' => '59000',
                'type_profil' => 'physique',
            ],
        ];

        foreach ($clients as $clientData) {
            $client = Utilisateur::create(array_merge($clientData, [
                'mot_de_passe' => Hash::make('password'),
                'pays' => 'France',
                'est_actif' => true,
            ]));
            $client->assignRole('client');
        }

        // Créer un client personne morale
        $entreprise = Utilisateur::create([
            'nom_complet' => 'Entreprise Tech Solutions',
            'email' => 'contact@techsolutions.fr',
            'mot_de_passe' => Hash::make('password'),
            'telephone' => '+33 1 40 50 60 70',
            'adresse' => '456 Avenue de la Grande Armée',
            'ville' => 'Paris',
            'code_postal' => '75017',
            'pays' => 'France',
            'est_actif' => true,
            'type_profil' => 'morale',
            'raison_sociale' => 'Tech Solutions SAS',
            'siret' => '12345678901234',
            'numero_tva' => 'FR12345678901',
            'forme_juridique' => 'SAS',
            'nom_contact' => 'Durand',
            'prenom_contact' => 'Thomas',
            'fonction_contact' => 'Directeur Achats',
        ]);
        $entreprise->assignRole('client');

        // Créer des fournisseurs
        $fournisseurs = [
            [
                'nom_complet' => 'Fournisseur TechDistrib',
                'email' => 'contact@techdistrib.fr',
                'raison_sociale' => 'TechDistrib France SAS',
                'siret' => '98765432109876',
            ],
            [
                'nom_complet' => 'Fournisseur ElectroPlus',
                'email' => 'ventes@electroplus.fr',
                'raison_sociale' => 'ElectroPlus Distribution SARL',
                'siret' => '11223344556677',
            ],
        ];

        foreach ($fournisseurs as $fournisseurData) {
            $fournisseur = Utilisateur::create([
                'nom_complet' => $fournisseurData['nom_complet'],
                'email' => $fournisseurData['email'],
                'mot_de_passe' => Hash::make('password'),
                'telephone' => '+33 1 42 43 44 45',
                'adresse' => '100 Rue du Commerce',
                'ville' => 'Paris',
                'code_postal' => '75015',
                'pays' => 'France',
                'est_actif' => true,
                'type_profil' => 'morale',
                'raison_sociale' => $fournisseurData['raison_sociale'],
                'siret' => $fournisseurData['siret'],
            ]);
            $fournisseur->assignRole('fournisseur');
        }

        $this->command->info('✅ Utilisateurs de test créés avec succès!');
        $this->command->info('   - 1 Admin: admin@fagmethod.com / password');
        $this->command->info('   - 5 Clients (4 physiques + 1 morale)');
        $this->command->info('   - 2 Fournisseurs');
    }
}
