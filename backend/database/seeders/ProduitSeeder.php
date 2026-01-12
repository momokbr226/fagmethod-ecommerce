<?php

namespace Database\Seeders;

use App\Models\Produit;
use App\Models\Categorie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProduitSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Categorie::all();
        
        if ($categories->isEmpty()) {
            $this->command->warn('Aucune catégorie trouvée. Exécutez d\'abord CategorieSeeder.');
            return;
        }

        $produits = [
            [
                'nom' => 'MacBook Pro 14"',
                'description' => 'Ordinateur portable Apple MacBook Pro 14 pouces avec puce M3 Pro, 18 Go de RAM et 512 Go de stockage SSD.',
                'description_courte' => 'MacBook Pro 14" M3 Pro - Puissance et élégance',
                'reference' => 'MBP14-M3PRO-512',
                'prix' => 2499.00,
                'prix_compare' => 2699.00,
                'quantite_stock' => 15,
                'est_actif' => true,
                'est_mise_en_avant' => true,
                'marque' => 'Apple',
                'modele' => 'MacBook Pro 14"',
                'garantie' => '2 ans constructeur',
                'categorie' => 'Ordinateurs',
            ],
            [
                'nom' => 'Dell XPS 15',
                'description' => 'Ordinateur portable Dell XPS 15 avec écran OLED 3.5K, Intel Core i7-13700H, 32 Go RAM, 1 To SSD.',
                'description_courte' => 'Dell XPS 15 - Performance et design premium',
                'reference' => 'DELL-XPS15-I7-32',
                'prix' => 1899.00,
                'prix_compare' => 2199.00,
                'quantite_stock' => 20,
                'est_actif' => true,
                'est_mise_en_avant' => true,
                'marque' => 'Dell',
                'modele' => 'XPS 15 9530',
                'garantie' => '2 ans constructeur',
                'categorie' => 'Ordinateurs',
            ],
            [
                'nom' => 'NVIDIA RTX 4080 Super',
                'description' => 'Carte graphique NVIDIA GeForce RTX 4080 Super avec 16 Go de GDDR6X, ray tracing et DLSS 3.',
                'description_courte' => 'RTX 4080 Super - Gaming 4K ultime',
                'reference' => 'NV-RTX4080S-16G',
                'prix' => 1199.00,
                'prix_compare' => 1399.00,
                'quantite_stock' => 8,
                'est_actif' => true,
                'est_mise_en_avant' => true,
                'marque' => 'NVIDIA',
                'modele' => 'RTX 4080 Super',
                'garantie' => '3 ans constructeur',
                'categorie' => 'Composants',
            ],
            [
                'nom' => 'AMD Ryzen 9 7950X',
                'description' => 'Processeur AMD Ryzen 9 7950X, 16 cœurs, 32 threads, 5.7 GHz boost, socket AM5.',
                'description_courte' => 'Ryzen 9 7950X - Puissance multi-cœur',
                'reference' => 'AMD-R9-7950X',
                'prix' => 549.00,
                'prix_compare' => 649.00,
                'quantite_stock' => 25,
                'est_actif' => true,
                'est_mise_en_avant' => true,
                'marque' => 'AMD',
                'modele' => 'Ryzen 9 7950X',
                'garantie' => '3 ans constructeur',
                'categorie' => 'Composants',
            ],
            [
                'nom' => 'Logitech MX Master 3S',
                'description' => 'Souris sans fil Logitech MX Master 3S avec capteur 8000 DPI, molette MagSpeed et connexion multi-appareils.',
                'description_courte' => 'MX Master 3S - Productivité maximale',
                'reference' => 'LOG-MXM3S-BLK',
                'prix' => 99.00,
                'prix_compare' => 119.00,
                'quantite_stock' => 50,
                'est_actif' => true,
                'est_mise_en_avant' => true,
                'marque' => 'Logitech',
                'modele' => 'MX Master 3S',
                'garantie' => '2 ans constructeur',
                'categorie' => 'Périphériques',
            ],
            [
                'nom' => 'Samsung 990 Pro 2To',
                'description' => 'SSD NVMe Samsung 990 Pro 2 To, lecture 7450 Mo/s, écriture 6900 Mo/s, PCIe 4.0.',
                'description_courte' => 'Samsung 990 Pro - Vitesse extrême',
                'reference' => 'SAM-990PRO-2TB',
                'prix' => 179.00,
                'prix_compare' => 229.00,
                'quantite_stock' => 40,
                'est_actif' => true,
                'est_mise_en_avant' => true,
                'marque' => 'Samsung',
                'modele' => '990 Pro',
                'garantie' => '5 ans constructeur',
                'categorie' => 'Stockage',
            ],
            [
                'nom' => 'ASUS ROG Swift PG32UQX',
                'description' => 'Moniteur gaming ASUS ROG Swift 32 pouces 4K 144Hz avec Mini LED, HDR 1400 et G-Sync Ultimate.',
                'description_courte' => 'ROG Swift - Gaming 4K HDR ultime',
                'reference' => 'ASUS-PG32UQX',
                'prix' => 2999.00,
                'prix_compare' => 3499.00,
                'quantite_stock' => 5,
                'est_actif' => true,
                'est_mise_en_avant' => true,
                'marque' => 'ASUS',
                'modele' => 'ROG Swift PG32UQX',
                'garantie' => '3 ans constructeur',
                'categorie' => 'Périphériques',
            ],
            [
                'nom' => 'TP-Link Archer AX6000',
                'description' => 'Routeur WiFi 6 TP-Link Archer AX6000 avec 8 antennes, 4804 Mbps sur 5 GHz et ports 2.5 Gbps.',
                'description_courte' => 'Archer AX6000 - WiFi 6 ultra-rapide',
                'reference' => 'TPL-AX6000',
                'prix' => 299.00,
                'prix_compare' => 349.00,
                'quantite_stock' => 18,
                'est_actif' => true,
                'est_mise_en_avant' => true,
                'marque' => 'TP-Link',
                'modele' => 'Archer AX6000',
                'garantie' => '3 ans constructeur',
                'categorie' => 'Réseau',
            ],
        ];

        foreach ($produits as $produit) {
            $categorie = $categories->firstWhere('nom', $produit['categorie']);
            
            Produit::create([
                'nom' => $produit['nom'],
                'slug' => Str::slug($produit['nom']),
                'description' => $produit['description'],
                'description_courte' => $produit['description_courte'],
                'reference' => $produit['reference'],
                'prix' => $produit['prix'],
                'prix_compare' => $produit['prix_compare'],
                'quantite_stock' => $produit['quantite_stock'],
                'est_actif' => $produit['est_actif'],
                'est_mise_en_avant' => $produit['est_mise_en_avant'],
                'marque' => $produit['marque'],
                'modele' => $produit['modele'],
                'garantie' => $produit['garantie'],
                'categorie_id' => $categorie?->id,
            ]);
        }
    }
}
