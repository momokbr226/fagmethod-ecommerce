<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $laptopCategory = Category::where('slug', 'laptops-pro')->first();
        $gamingLaptopCategory = Category::where('slug', 'laptops-gaming')->first();
        $ultrabookCategory = Category::where('slug', 'ultrabooks')->first();
        $pcGamingCategory = Category::where('slug', 'pc-gaming')->first();
        $workstationCategory = Category::where('slug', 'workstations')->first();
        $pcBureautiqueCategory = Category::where('slug', 'pc-bureautique')->first();
        $processorCategory = Category::where('slug', 'processeurs')->first();
        $graphicsCategory = Category::where('slug', 'cartes-graphiques')->first();
        $storageCategory = Category::where('slug', 'stockage')->first();
        $keyboardMouseCategory = Category::where('slug', 'claviers-souris')->first();
        $monitorCategory = Category::where('slug', 'ecrans')->first();
        $audioCategory = Category::where('slug', 'audio')->first();

        // Laptops Pro
        Product::factory()->laptop()->count(5)->create([
            'category_id' => $laptopCategory?->id,
        ]);

        // Laptops Gaming
        Product::factory()->laptop()->count(8)->create([
            'category_id' => $gamingLaptopCategory?->id,
            'attributes' => [
                'brand' => 'ASUS ROG',
                'warranty' => '2 years',
                'color' => 'Black',
                'processor' => 'Intel i7',
                'ram' => '16GB',
                'storage' => '1TB SSD',
                'screen_size' => '15.6"',
                'gpu' => 'NVIDIA RTX 3060',
                'refresh_rate' => '144Hz',
            ],
        ]);

        // Ultrabooks
        Product::factory()->laptop()->count(6)->create([
            'category_id' => $ultrabookCategory?->id,
            'price' => fn() => fake()->randomFloat(2, 800, 1500),
            'attributes' => [
                'brand' => 'Dell XPS',
                'warranty' => '1 year',
                'color' => 'Silver',
                'processor' => 'Intel i5',
                'ram' => '8GB',
                'storage' => '512GB SSD',
                'screen_size' => '13.3"',
                'weight' => '1.2kg',
                'battery_life' => '12 hours',
            ],
        ]);

        // PC Gaming
        Product::factory()->desktop()->count(10)->create([
            'category_id' => $pcGamingCategory?->id,
            'price' => fn() => fake()->randomFloat(2, 1200, 3000),
            'attributes' => [
                'brand' => 'MSI',
                'warranty' => '2 years',
                'color' => 'Black',
                'processor' => 'Intel i7',
                'ram' => '16GB',
                'storage' => '1TB SSD',
                'graphics' => 'NVIDIA RTX 3070',
                'cooling' => 'Liquid Cooling',
                'case_type' => 'Mid Tower',
            ],
        ]);

        // Workstations
        Product::factory()->desktop()->count(4)->create([
            'category_id' => $workstationCategory?->id,
            'price' => fn() => fake()->randomFloat(2, 2000, 5000),
            'attributes' => [
                'brand' => 'HP',
                'warranty' => '3 years',
                'color' => 'Gray',
                'processor' => 'Intel i9',
                'ram' => '32GB',
                'storage' => '2TB SSD',
                'graphics' => 'NVIDIA RTX 3060',
                'certified' => 'ISV Certified',
                'ecc_memory' => true,
            ],
        ]);

        // PC Bureautique
        Product::factory()->desktop()->count(8)->create([
            'category_id' => $pcBureautiqueCategory?->id,
            'price' => fn() => fake()->randomFloat(2, 400, 800),
            'attributes' => [
                'brand' => 'Lenovo',
                'warranty' => '2 years',
                'color' => 'Black',
                'processor' => 'Intel i5',
                'ram' => '8GB',
                'storage' => '512GB SSD',
                'graphics' => 'Integrated',
                'form_factor' => 'Mini Tower',
            ],
        ]);

        // Processeurs
        Product::factory()->count(15)->create([
            'category_id' => $processorCategory?->id,
            'price' => fn() => fake()->randomFloat(2, 150, 800),
            'name' => fn() => fake()->randomElement(['Intel Core i5', 'Intel Core i7', 'Intel Core i9', 'AMD Ryzen 5', 'AMD Ryzen 7', 'AMD Ryzen 9']) . ' ' . fake()->randomElement(['12400F', '12600K', '12700K', '5600X', '5800X', '5900X']),
            'attributes' => [
                'brand' => fn() => fake()->randomElement(['Intel', 'AMD']),
                'warranty' => '3 years',
                'socket' => fn() => fake()->randomElement(['LGA 1700', 'AM4', 'AM5']),
                'cores' => fn() => fake()->randomElement([6, 8, 12, 16]),
                'threads' => fn() => fake()->randomElement([12, 16, 24, 32]),
                'base_clock' => fn() => fake()->randomFloat(1, 2.5, 4.0) . 'GHz',
                'boost_clock' => fn() => fake()->randomFloat(1, 4.0, 5.5) . 'GHz',
                'tdp' => fn() => fake()->randomElement([65, 95, 125, 170]) . 'W',
            ],
        ]);

        // Cartes Graphiques
        Product::factory()->count(12)->create([
            'category_id' => $graphicsCategory?->id,
            'price' => fn() => fake()->randomFloat(2, 300, 2000),
            'name' => fn() => fake()->randomElement(['NVIDIA', 'AMD']) . ' ' . fake()->randomElement(['RTX 3060', 'RTX 3070', 'RTX 3080', 'RTX 4060', 'RTX 4070', 'RX 6700 XT', 'RX 6800 XT', 'RX 7800 XT']),
            'attributes' => [
                'brand' => fn() => fake()->randomElement(['NVIDIA', 'AMD']),
                'warranty' => '2 years',
                'memory' => fn() => fake()->randomElement(['8GB', '12GB', '16GB']),
                'memory_type' => 'GDDR6',
                'base_clock' => fn() => fake()->randomFloat(1, 1.5, 2.5) . 'GHz',
                'boost_clock' => fn() => fake()->randomFloat(1, 2.0, 3.0) . 'GHz',
                'power_consumption' => fn() => fake()->randomElement([150, 200, 250, 350]) . 'W',
                'length' => fn() => fake()->randomElement([280, 300, 320, 350]) . 'mm',
            ],
        ]);

        // Stockage
        Product::factory()->count(20)->create([
            'category_id' => $storageCategory?->id,
            'price' => fn() => fake()->randomFloat(2, 50, 500),
            'name' => fn() => fake()->randomElement(['Samsung', 'Crucial', 'WD', 'Kingston']) . ' ' . fake()->randomElement(['870 EVO', 'MX500', 'Blue SSD', 'NV2']) . ' ' . fake()->randomElement(['500GB', '1TB', '2TB']),
            'attributes' => [
                'brand' => fn() => fake()->randomElement(['Samsung', 'Crucial', 'WD', 'Kingston']),
                'warranty' => '3 years',
                'capacity' => fn() => fake()->randomElement(['500GB', '1TB', '2TB', '4TB']),
                'type' => fn() => fake()->randomElement(['SATA SSD', 'NVMe SSD', 'HDD']),
                'interface' => fn() => fake()->randomElement(['SATA III', 'PCIe 3.0', 'PCIe 4.0']),
                'read_speed' => fn() => fake()->randomElement([500, 1000, 3500, 7000]) . ' MB/s',
                'write_speed' => fn() => fake()->randomElement([400, 800, 3000, 6500]) . ' MB/s',
            ],
        ]);

        // Claviers et Souris
        Product::factory()->accessory()->count(25)->create([
            'category_id' => $keyboardMouseCategory?->id,
            'price' => fn() => fake()->randomFloat(2, 20, 200),
            'attributes' => [
                'brand' => fn() => fake()->randomElement(['Logitech', 'Razer', 'Corsair', 'SteelSeries']),
                'warranty' => '2 years',
                'type' => fn() => fake()->randomElement(['Gaming Keyboard', 'Office Keyboard', 'Gaming Mouse', 'Office Mouse']),
                'connectivity' => fn() => fake()->randomElement(['USB', 'Wireless', 'Bluetooth']),
                'backlight' => fn() => fake()->randomElement(['None', 'RGB', 'Single Color']),
                'switch_type' => fn() => fake()->randomElement(['Mechanical', 'Membrane', 'Scissor']),
            ],
        ]);

        // Écrans
        Product::factory()->count(15)->create([
            'category_id' => $monitorCategory?->id,
            'price' => fn() => fake()->randomFloat(2, 200, 1500),
            'name' => fn() => fake()->randomElement(['LG', 'Samsung', 'Dell', 'ASUS']) . ' ' . fake()->randomElement(['27"', '32"', '24"']) . ' ' . fake()->randomElement(['4K', '144Hz', 'Gaming', 'Professional']),
            'attributes' => [
                'brand' => fn() => fake()->randomElement(['LG', 'Samsung', 'Dell', 'ASUS']),
                'warranty' => '3 years',
                'type' => 'Monitor',
                'screen_size' => fn() => fake()->randomElement(['24"', '27"', '32"', '34"']),
                'resolution' => fn() => fake()->randomElement(['1920x1080', '2560x1440', '3840x2160', '3440x1440']),
                'refresh_rate' => fn() => fake()->randomElement([60, 75, 144, 165, 240]) . 'Hz',
                'panel_type' => fn() => fake()->randomElement(['IPS', 'VA', 'TN']),
                'response_time' => fn() => fake()->randomElement([1, 2, 5]) . 'ms',
            ],
        ]);

        // Audio
        Product::factory()->accessory()->count(18)->create([
            'category_id' => $audioCategory?->id,
            'price' => fn() => fake()->randomFloat(2, 30, 300),
            'attributes' => [
                'brand' => fn() => fake()->randomElement(['Sony', 'Bose', 'Sennheiser', 'JBL']),
                'warranty' => '1 year',
                'type' => fn() => fake()->randomElement(['Headset', 'Headphones', 'Speakers', 'Microphone']),
                'connectivity' => fn() => fake()->randomElement(['USB', 'Bluetooth', '3.5mm', 'Wireless']),
                'noise_cancelling' => fn() => fake()->boolean(50),
                'surround_sound' => fn() => fake()->boolean(70),
            ],
        ]);
    }
}
