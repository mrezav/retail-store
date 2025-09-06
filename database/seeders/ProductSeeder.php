<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Smartphone XYZ',
            'slug' => 'smartphone-xyz',
            'description' => 'A high-end smartphone with excellent features.',
            'price' => 699.99,
            'stock' => 50,
            'is_active' => true,
            'merk' => 'BrandA',
            'color' => 'Black',
            'size' => '6.5 inch',
            'category_id' => 1, // Assuming category with ID 1 exists
            'image_path' => 'products/smartphone_xyz.jpg'
        ]);
    }
}
