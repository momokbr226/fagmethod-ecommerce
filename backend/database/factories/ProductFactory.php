<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);
        $price = fake()->randomFloat(2, 50, 2000);
        $hasDiscount = fake()->boolean(30);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraphs(3, true),
            'short_description' => fake()->sentence(15),
            'sku' => 'SKU-' . fake()->unique()->numerify('######'),
            'price' => $price,
            'compare_price' => $hasDiscount ? $price * fake()->randomFloat(2, 1.2, 1.5) : null,
            'stock_quantity' => fake()->numberBetween(0, 100),
            'track_quantity' => true,
            'is_active' => true,
            'image' => 'https://picsum.photos/640/480?random=' . fake()->unique()->numberBetween(1, 1000),
            'images' => fake()->randomElements([
                'https://picsum.photos/640/480?random=' . fake()->unique()->numberBetween(1, 1000),
                'https://picsum.photos/640/480?random=' . fake()->unique()->numberBetween(1, 1000),
                'https://picsum.photos/640/480?random=' . fake()->unique()->numberBetween(1, 1000),
            ], fake()->numberBetween(1, 3)),
            'weight' => fake()->randomFloat(2, 0.1, 10),
            'attributes' => [
                'brand' => fake()->randomElement(['Dell', 'HP', 'Lenovo', 'Apple', 'ASUS', 'MSI']),
                'warranty' => fake()->randomElement(['1 year', '2 years', '3 years']),
                'color' => fake()->randomElement(['Black', 'Silver', 'White', 'Gray']),
            ],
            'category_id' => null,
        ];
    }

    public function laptop(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->words(3, true) . ' Laptop',
            'attributes' => array_merge($attributes['attributes'] ?? [], [
                'processor' => fake()->randomElement(['Intel i5', 'Intel i7', 'AMD Ryzen 5', 'AMD Ryzen 7']),
                'ram' => fake()->randomElement(['8GB', '16GB', '32GB']),
                'storage' => fake()->randomElement(['256GB SSD', '512GB SSD', '1TB SSD']),
                'screen_size' => fake()->randomElement(['13.3"', '14"', '15.6"', '17.3"']),
            ]),
        ]);
    }

    public function desktop(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->words(3, true) . ' Desktop PC',
            'attributes' => array_merge($attributes['attributes'] ?? [], [
                'processor' => fake()->randomElement(['Intel i5', 'Intel i7', 'Intel i9', 'AMD Ryzen 5', 'AMD Ryzen 7', 'AMD Ryzen 9']),
                'ram' => fake()->randomElement(['8GB', '16GB', '32GB', '64GB']),
                'storage' => fake()->randomElement(['512GB SSD', '1TB SSD', '2TB SSD']),
                'graphics' => fake()->randomElement(['Integrated', 'NVIDIA GTX 1650', 'NVIDIA RTX 3060', 'NVIDIA RTX 3070']),
            ]),
        ]);
    }

    public function accessory(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->words(2, true) . ' Accessory',
            'price' => fake()->randomFloat(2, 10, 200),
            'attributes' => array_merge($attributes['attributes'] ?? [], [
                'type' => fake()->randomElement(['Mouse', 'Keyboard', 'Monitor', 'Webcam', 'Headset']),
                'connectivity' => fake()->randomElement(['USB', 'USB-C', 'Wireless', 'Bluetooth']),
            ]),
        ]);
    }
}
