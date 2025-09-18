<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Obeng',
            'slug' => 'obeng',
            'description' => 'obeng kualitas bagus',
            'is_active' => true,
            'image_path' => 'products/obeng.jpg',
            'category_id' => 2, // Assuming category with ID 1 exists
        ]);
        Product::create([
            'name' => 'Stop kontak',
            'slug' => 'stop-kontak',
            'description' => 'stop kontak listrik',
            'is_active' => true,
            'image_path' => 'products/obeng.jpg',
            'category_id' => 1, // Assuming category with ID 1 exists
        ]);
        Product::create([
            'name' => 'Terminal',
            'slug' => 'terminal',
            'description' => 'terminal listrik',
            'is_active' => true,
            'image_path' => 'products/obeng.jpg',
            'category_id' => 1, // Assuming category with ID 1 exists
        ]);
        Product::create([
            'name' => 'Perkakas',
            'slug' => 'perkakas',
            'description' => 'Barang ini adalah kumpulan dari perkakas rumah yang sering digunakan sehari-hari untuk kebutuhan perbaikan dan semacamnya.',
            'is_active' => true,
            'image_path' => 'products/obeng.jpg',
            'category_id' => 1, // Assuming category with ID 1 exists
        ]);
    }
}
