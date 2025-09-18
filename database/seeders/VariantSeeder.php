<?php

namespace Database\Seeders;

use App\Models\Variant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Variant::create([
            'product_id'=> 1, 
            'merk'=> 'nankai', 
            'color'=> '', 
            'dimension'=> '', 
            'stock'=> 3, 
            'price'=> 3000, 
        ]);
        Variant::create([
            'product_id'=> 2, 
            'merk'=> 'panasonic', 
            'color'=> '', 
            'dimension'=> '', 
            'stock'=> 4, 
            'price'=> 4000, 
        ]);
        Variant::create([
            'product_id'=> 2, 
            'merk'=> 'broco', 
            'color'=> 'hitam', 
            'dimension'=> '4cm', 
            'stock'=> 8, 
            'price'=> 15000, 
        ]);
        Variant::create([
            'product_id'=> 3, 
            'merk'=> 'krisbow', 
            'color'=> 'putih', 
            'dimension'=> '12 inch', 
            'stock'=> 25, 
            'price'=> 35000, 
        ]);
    }
}
