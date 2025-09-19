<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Alat listrik',
            'slug' => 'alat-listrik',
            'description' => 'peralatan listrik',
            'image_path' => ''
        ]);
        
        Category::create([
            'name' => 'Perkakas',
            'slug' => 'perkakas',
            'description' => 'perkakas rumah',
            'image_path' => ''
        ]);
        
    }
}
