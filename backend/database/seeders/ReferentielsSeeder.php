<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Marque;
use App\Models\FamilleProduit;
use App\Models\Fournisseur;
use App\Models\Parametre;
use Illuminate\Support\Str;

class ReferentielsSeeder extends Seeder
{
    public function run(): void
    {
        // Créer des marques
        $marques = [
            ['nom' => 'Samsung', 'pays_origine' => 'Corée du Sud', 'site_web' => 'https://www.samsung.com'],
            ['nom' => 'Apple', 'pays_origine' => 'États-Unis', 'site_web' => 'https://www.apple.com'],
            ['nom' => 'Sony', 'pays_origine' => 'Japon', 'site_web' => 'https://www.sony.com'],
            ['nom' => 'LG', 'pays_origine' => 'Corée du Sud', 'site_web' => 'https://www.lg.com'],
            ['nom' => 'Dell', 'pays_origine' => 'États-Unis', 'site_web' => 'https://www.dell.com'],
            ['nom' => 'HP', 'pays_origine' => 'États-Unis', 'site_web' => 'https://www.hp.com'],
            ['nom' => 'Lenovo', 'pays_origine' => 'Chine', 'site_web' => 'https://www.lenovo.com'],
            ['nom' => 'Asus', 'pays_origine' => 'Taïwan', 'site_web' => 'https://www.asus.com'],
        ];

        foreach ($marques as $index => $marque) {
            Marque::create([
                'nom' => $marque['nom'],
                'slug' => Str::slug($marque['nom']),
                'pays_origine' => $marque['pays_origine'],
                'site_web' => $marque['site_web'],
                'description' => "Marque leader dans le domaine de l'électronique et de l'informatique",
                'est_active' => true,
                'ordre' => $index + 1,
            ]);
        }

        // Créer des familles de produits
        $familles = [
            ['nom' => 'Smartphones', 'code' => 'SMART', 'description' => 'Téléphones intelligents et accessoires'],
            ['nom' => 'Ordinateurs portables', 'code' => 'LAPTOP', 'description' => 'Ordinateurs portables et ultrabooks'],
            ['nom' => 'Ordinateurs de bureau', 'code' => 'DESKTOP', 'description' => 'PC de bureau et stations de travail'],
            ['nom' => 'Tablettes', 'code' => 'TABLET', 'description' => 'Tablettes tactiles et liseuses'],
            ['nom' => 'Téléviseurs', 'code' => 'TV', 'description' => 'Téléviseurs et écrans'],
            ['nom' => 'Audio', 'code' => 'AUDIO', 'description' => 'Casques, enceintes et systèmes audio'],
            ['nom' => 'Photo & Vidéo', 'code' => 'PHOTO', 'description' => 'Appareils photo et caméras'],
            ['nom' => 'Accessoires', 'code' => 'ACCESS', 'description' => 'Accessoires informatiques et électroniques'],
        ];

        foreach ($familles as $index => $famille) {
            FamilleProduit::create([
                'nom' => $famille['nom'],
                'slug' => Str::slug($famille['nom']),
                'code' => $famille['code'],
                'description' => $famille['description'],
                'est_active' => true,
                'ordre' => $index + 1,
            ]);
        }

        // Créer des fournisseurs
        $fournisseurs = [
            [
                'nom' => 'TechDistrib France',
                'raison_sociale' => 'TechDistrib France SAS',
                'code_fournisseur' => 'TECH001',
                'siret' => '12345678901234',
                'email' => 'contact@techdistrib.fr',
                'telephone' => '0142345678',
                'ville' => 'Paris',
                'code_postal' => '75001',
                'delai_livraison_moyen' => 3.5,
                'conditions_paiement' => '30_jours',
            ],
            [
                'nom' => 'ElectroPlus',
                'raison_sociale' => 'ElectroPlus Distribution SARL',
                'code_fournisseur' => 'ELEC001',
                'siret' => '98765432109876',
                'email' => 'commandes@electroplus.fr',
                'telephone' => '0143456789',
                'ville' => 'Lyon',
                'code_postal' => '69001',
                'delai_livraison_moyen' => 2.0,
                'conditions_paiement' => '60_jours',
            ],
            [
                'nom' => 'Informatique Pro',
                'raison_sociale' => 'Informatique Pro SAS',
                'code_fournisseur' => 'INFO001',
                'siret' => '11223344556677',
                'email' => 'ventes@infopro.fr',
                'telephone' => '0144567890',
                'ville' => 'Marseille',
                'code_postal' => '13001',
                'delai_livraison_moyen' => 5.0,
                'conditions_paiement' => '30_jours',
            ],
        ];

        foreach ($fournisseurs as $fournisseur) {
            Fournisseur::create($fournisseur);
        }

        $this->command->info('Référentiels créés avec succès!');
        $this->command->info('- 8 Marques');
        $this->command->info('- 8 Familles de produits');
        $this->command->info('- 3 Fournisseurs');
    }
}
