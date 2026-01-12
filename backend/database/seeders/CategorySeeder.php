<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Ordinateurs Portables',
                'slug' => 'ordinateurs-portables',
                'description' => 'Découvrez notre sélection d\'ordinateurs portables performants pour tous les usages.',
                'is_active' => true,
                'sort_order' => 1,
                'children' => [
                    [
                        'name' => 'Laptops Pro',
                        'slug' => 'laptops-pro',
                        'description' => 'Ordinateurs portables haut de gamme pour les professionnels.',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Laptops Gaming',
                        'slug' => 'laptops-gaming',
                        'description' => 'PC portables optimisés pour le gaming.',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Ultrabooks',
                        'slug' => 'ultrabooks',
                        'description' => 'Ordinateurs portables fins et légers.',
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Ordinateurs de Bureau',
                'slug' => 'ordinateurs-de-bureau',
                'description' => 'PC de bureau puissants pour le travail et le divertissement.',
                'is_active' => true,
                'sort_order' => 2,
                'children' => [
                    [
                        'name' => 'PC Gaming',
                        'slug' => 'pc-gaming',
                        'description' => 'Ordinateurs de bureau optimisés pour les jeux vidéo.',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Workstations',
                        'slug' => 'workstations',
                        'description' => 'Stations de travail haute performance.',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'PC Bureautique',
                        'slug' => 'pc-bureautique',
                        'description' => 'Ordinateurs pour un usage bureautique quotidien.',
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Composants',
                'slug' => 'composants',
                'description' => 'Composants informatiques pour assembler ou mettre à niveau votre PC.',
                'is_active' => true,
                'sort_order' => 3,
                'children' => [
                    [
                        'name' => 'Processeurs',
                        'slug' => 'processeurs',
                        'description' => 'CPU Intel et AMD de dernière génération.',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Cartes Mères',
                        'slug' => 'cartes-meres',
                        'description' => 'Motherboards pour tous les besoins.',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Mémoire RAM',
                        'slug' => 'memoire-ram',
                        'description' => 'Barrettes de mémoire RAM DDR4 et DDR5.',
                        'sort_order' => 3,
                    ],
                    [
                        'name' => 'Cartes Graphiques',
                        'slug' => 'cartes-graphiques',
                        'description' => 'GPU NVIDIA et AMD pour gaming et création.',
                        'sort_order' => 4,
                    ],
                    [
                        'name' => 'Stockage',
                        'slug' => 'stockage',
                        'description' => 'SSD et HDD pour tous les besoins.',
                        'sort_order' => 5,
                    ],
                ],
            ],
            [
                'name' => 'Périphériques',
                'slug' => 'peripheriques',
                'description' => 'Accessoires et périphériques pour compléter votre setup.',
                'is_active' => true,
                'sort_order' => 4,
                'children' => [
                    [
                        'name' => 'Claviers et Souris',
                        'slug' => 'claviers-souris',
                        'description' => 'Claviers et souris pour gaming et bureau.',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Écrans',
                        'slug' => 'ecrans',
                        'description' => 'Moniteurs pour tous les usages.',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Audio',
                        'slug' => 'audio',
                        'description' => 'Casques, enceintes et microphones.',
                        'sort_order' => 3,
                    ],
                    [
                        'name' => 'Webcams',
                        'slug' => 'webcams',
                        'description' => 'Webcams HD pour visioconférence.',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'name' => 'Réseau',
                'slug' => 'reseau',
                'description' => 'Matériel réseau pour connectivité optimale.',
                'is_active' => true,
                'sort_order' => 5,
                'children' => [
                    [
                        'name' => 'Routeurs et WiFi',
                        'slug' => 'routeurs-wifi',
                        'description' => 'Routeurs et points d\'accès WiFi.',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Switches',
                        'slug' => 'switches',
                        'description' => 'Switches réseau pour professionnels.',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Câbles',
                        'slug' => 'cables',
                        'description' => 'Câbles Ethernet et connectiques.',
                        'sort_order' => 3,
                    ],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $category = Category::create($categoryData);

            foreach ($children as $childData) {
                $childData['parent_id'] = $category->id;
                Category::create($childData);
            }
        }
    }
}
