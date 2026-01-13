<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produit;
use App\Models\Categorie;
use App\Models\Marque;
use App\Models\FamilleProduit;
use App\Models\Fournisseur;
use Illuminate\Support\Str;

class ProduitsTestSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les données de référence
        $categories = Categorie::all();
        $marques = Marque::all();
        $familles = FamilleProduit::all();
        $fournisseurs = Fournisseur::all();

        if ($categories->isEmpty() || $marques->isEmpty() || $familles->isEmpty()) {
            $this->command->warn('⚠️  Veuillez d\'abord exécuter ReferentielsSeeder');
            return;
        }

        // Produits Smartphones
        $smartphones = [
            [
                'nom' => 'Samsung Galaxy S24 Ultra',
                'description' => 'Le smartphone le plus puissant de Samsung avec un écran Dynamic AMOLED 2X de 6.8 pouces, processeur Snapdragon 8 Gen 3, 12GB RAM, 256GB stockage, et un système de caméra quadruple avec zoom optique 10x.',
                'description_courte' => 'Smartphone haut de gamme avec écran 6.8" et caméra 200MP',
                'prix' => 1299.99,
                'quantite_stock' => 25,
                'reference' => 'SAM-S24U-256',
                'caracteristiques' => [
                    'Écran' => '6.8" Dynamic AMOLED 2X',
                    'Processeur' => 'Snapdragon 8 Gen 3',
                    'RAM' => '12GB',
                    'Stockage' => '256GB',
                    'Caméra' => '200MP + 50MP + 12MP + 10MP',
                    'Batterie' => '5000mAh'
                ]
            ],
            [
                'nom' => 'iPhone 15 Pro Max',
                'description' => 'L\'iPhone le plus avancé avec puce A17 Pro, écran Super Retina XDR de 6.7 pouces, système de caméra Pro avec zoom optique 5x, et cadre en titane.',
                'description_courte' => 'iPhone Pro avec puce A17 Pro et cadre titane',
                'prix' => 1479.99,
                'prix_promo' => 1399.99,
                'date_debut_promo' => now(),
                'date_fin_promo' => now()->addDays(30),
                'est_promo' => true,
                'quantite_stock' => 18,
                'reference' => 'APL-IP15PM-256',
                'caracteristiques' => [
                    'Écran' => '6.7" Super Retina XDR',
                    'Puce' => 'A17 Pro',
                    'Stockage' => '256GB',
                    'Caméra' => 'Triple 48MP',
                    'Batterie' => 'Jusqu\'à 29h vidéo'
                ]
            ],
            [
                'nom' => 'Google Pixel 8 Pro',
                'description' => 'Smartphone Google avec intelligence artificielle avancée, écran OLED 6.7 pouces 120Hz, puce Tensor G3, et les meilleures capacités photo grâce à l\'IA.',
                'description_courte' => 'Smartphone IA avec puce Tensor G3',
                'prix' => 1099.99,
                'quantite_stock' => 15,
                'reference' => 'GOO-PIX8P-256',
            ],
        ];

        // Produits Ordinateurs Portables
        $laptops = [
            [
                'nom' => 'MacBook Pro 14" M3 Pro',
                'description' => 'MacBook Pro avec puce M3 Pro, écran Liquid Retina XDR 14 pouces, 18GB RAM unifiée, SSD 512GB, autonomie jusqu\'à 18 heures.',
                'description_courte' => 'MacBook Pro M3 Pro 14 pouces',
                'prix' => 2499.99,
                'quantite_stock' => 12,
                'reference' => 'APL-MBP14-M3P',
                'est_vedette' => true,
            ],
            [
                'nom' => 'Dell XPS 15',
                'description' => 'Ordinateur portable premium avec écran InfinityEdge 15.6" 4K OLED, Intel Core i7-13700H, 16GB RAM, SSD 512GB, NVIDIA RTX 4050.',
                'description_courte' => 'Laptop premium avec écran 4K OLED',
                'prix' => 1899.99,
                'quantite_stock' => 8,
                'reference' => 'DEL-XPS15-I7',
            ],
            [
                'nom' => 'Lenovo ThinkPad X1 Carbon Gen 11',
                'description' => 'Ultrabook professionnel léger (1.12kg) avec écran 14" 2.8K, Intel Core i7-1365U, 16GB RAM, SSD 512GB, certification militaire MIL-STD-810H.',
                'description_courte' => 'Ultrabook professionnel ultra-léger',
                'prix' => 1699.99,
                'quantite_stock' => 10,
                'reference' => 'LEN-X1C11-I7',
            ],
            [
                'nom' => 'ASUS ROG Zephyrus G14',
                'description' => 'Laptop gaming compact avec écran 14" QHD+ 165Hz, AMD Ryzen 9 7940HS, 32GB RAM, SSD 1TB, NVIDIA RTX 4060.',
                'description_courte' => 'Laptop gaming compact et puissant',
                'prix' => 1799.99,
                'prix_promo' => 1649.99,
                'date_debut_promo' => now(),
                'date_fin_promo' => now()->addDays(15),
                'est_promo' => true,
                'quantite_stock' => 6,
                'reference' => 'ASU-G14-R9',
            ],
        ];

        // Produits Audio
        $audio = [
            [
                'nom' => 'Sony WH-1000XM5',
                'description' => 'Casque sans fil à réduction de bruit leader du marché, audio haute résolution, autonomie 30h, charge rapide, confort premium.',
                'description_courte' => 'Casque antibruit premium',
                'prix' => 399.99,
                'quantite_stock' => 30,
                'reference' => 'SON-WH1000XM5',
                'est_vedette' => true,
            ],
            [
                'nom' => 'Apple AirPods Pro 2',
                'description' => 'Écouteurs sans fil avec réduction de bruit active adaptative, audio spatial personnalisé, résistance à l\'eau IPX4.',
                'description_courte' => 'Écouteurs sans fil avec ANC',
                'prix' => 279.99,
                'quantite_stock' => 45,
                'reference' => 'APL-AIRPRO2',
            ],
        ];

        // Produits Tablettes
        $tablettes = [
            [
                'nom' => 'iPad Pro 12.9" M2',
                'description' => 'Tablette professionnelle avec écran Liquid Retina XDR 12.9", puce M2, 128GB, compatible Apple Pencil et Magic Keyboard.',
                'description_courte' => 'Tablette Pro avec puce M2',
                'prix' => 1199.99,
                'quantite_stock' => 15,
                'reference' => 'APL-IPADP129-M2',
                'est_nouveau' => true,
            ],
            [
                'nom' => 'Samsung Galaxy Tab S9 Ultra',
                'description' => 'Grande tablette Android avec écran AMOLED 14.6", Snapdragon 8 Gen 2, S Pen inclus, résistance à l\'eau IP68.',
                'description_courte' => 'Tablette Android premium 14.6"',
                'prix' => 1099.99,
                'quantite_stock' => 12,
                'reference' => 'SAM-TABS9U',
            ],
        ];

        // Créer tous les produits
        $allProducts = array_merge($smartphones, $laptops, $audio, $tablettes);
        
        foreach ($allProducts as $index => $productData) {
            // Déterminer la catégorie, marque et famille
            $categorie = $categories->random();
            $marque = $marques->random();
            $famille = $familles->random();
            $fournisseur = $fournisseurs->random();

            Produit::create([
                'nom' => $productData['nom'],
                'slug' => Str::slug($productData['nom']),
                'reference' => $productData['reference'],
                'description' => $productData['description'] ?? 'Description du produit ' . $productData['nom'],
                'description_courte' => $productData['description_courte'] ?? '',
                'categorie_id' => $categorie->id,
                'marque_id' => $marque->id,
                'famille_id' => $famille->id,
                'fournisseur_id' => $fournisseur->id,
                'prix' => $productData['prix'],
                'prix_promo' => $productData['prix_promo'] ?? null,
                'date_debut_promo' => $productData['date_debut_promo'] ?? null,
                'date_fin_promo' => $productData['date_fin_promo'] ?? null,
                'prix_achat' => $productData['prix'] * 0.6, // Marge de 40%
                'quantite_stock' => $productData['quantite_stock'],
                'seuil_alerte_stock' => 5,
                'statut_stock' => $productData['quantite_stock'] > 10 ? 'en_stock' : 'stock_faible',
                'gestion_stock' => true,
                'poids' => rand(200, 2000) / 100, // Entre 0.2 et 20 kg
                'est_visible' => true,
                'est_nouveau' => $productData['est_nouveau'] ?? ($index < 3),
                'est_vedette' => $productData['est_vedette'] ?? ($index % 4 == 0),
                'est_promo' => $productData['est_promo'] ?? false,
                'ordre' => $index,
                'caracteristiques' => $productData['caracteristiques'] ?? [],
                'vues' => rand(50, 500),
                'ventes' => rand(5, 50),
                'note_moyenne' => rand(35, 50) / 10, // Entre 3.5 et 5.0
                'nombre_avis' => rand(5, 50),
            ]);
        }

        $this->command->info('✅ ' . count($allProducts) . ' produits de test créés avec succès!');
        $this->command->info('   - Smartphones, Laptops, Audio, Tablettes');
        $this->command->info('   - Avec prix, stocks, promotions, et caractéristiques');
    }
}
