<?php

namespace Database\Seeders;

use App\Models\VariantImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VariantImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VariantImage::create([
            'variant_id' => 1, 'path' => 'product/obeng.jpg'
        ]);
        VariantImage::create([
            'variant_id' => 1, 'path' => 'product/obeng1.jpg'
        ]);
        VariantImage::create([
            'variant_id' => 2, 'path' => 'product/default.jpg'
        ]);
        VariantImage::create([
            'variant_id' => 3, 'path' => 'product/default.jpg'
        ]);
        VariantImage::create([
            'variant_id' => 4, 'path' => 'product/default.jpg'
        ]);
    }
}
