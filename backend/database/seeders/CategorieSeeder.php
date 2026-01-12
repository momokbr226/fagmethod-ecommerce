<?php

namespace Database\Seeders;

use App\Models\Categorie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorieSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'nom' => 'Ordinateurs',
                'description' => 'Ordinateurs portables et de bureau',
                'couleur' => '#3B82F6',
                'ordre_affichage' => 1,
            ],
            [
                'nom' => 'Composants',
                'description' => 'Composants informatiques et pièces détachées',
                'couleur' => '#10B981',
                'ordre_affichage' => 2,
            ],
            [
                'nom' => 'Périphériques',
                'description' => 'Claviers, souris, écrans et accessoires',
                'couleur' => '#F59E0B',
                'ordre_affichage' => 3,
            ],
            [
                'nom' => 'Stockage',
                'description' => 'Disques durs, SSD et solutions de stockage',
                'couleur' => '#EF4444',
                'ordre_affichage' => 4,
            ],
            [
                'nom' => 'Réseau',
                'description' => 'Équipements réseau et connectivité',
                'couleur' => '#8B5CF6',
                'ordre_affichage' => 5,
            ],
        ];

        foreach ($categories as $category) {
            Categorie::create([
                'nom' => $category['nom'],
                'slug' => Str::slug($category['nom']),
                'description' => $category['description'],
                'couleur' => $category['couleur'],
                'ordre_affichage' => $category['ordre_affichage'],
                'est_active' => true,
            ]);
        }
    }
}
