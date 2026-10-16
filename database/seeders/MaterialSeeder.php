<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use Illuminate\Support\Str;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        // ID 2 adalah kategori "Jenis Kain" (bisa Anda rename jadi "Jenis Bahan" di database)
        $categoryId = 2; 

        $materials = [
            [
                'category_id' => $categoryId,
                'name' => 'Flexi China',
                'slug' => 'flexi-china',
                'description' => 'Bahan banner standar, ekonomis, cocok untuk indoor/outdoor jangka pendek.',
                'price_per_m2' => 15000.00, // Rp 15.000/m2
                'stock' => 100,
                'is_active' => true,
            ],
            [
                'category_id' => $categoryId,
                'name' => 'Flexi Korea',
                'slug' => 'flexi-korea',
                'description' => 'Bahan banner kualitas lebih tinggi, warna lebih tajam dan tahan lama.',
                'price_per_m2' => 22000.00, // Rp 22.000/m2
                'stock' => 50,
                'is_active' => true,
            ],
            [
                'category_id' => $categoryId,
                'name' => 'Art Paper 150gr',
                'slug' => 'art-paper-150gr',
                'description' => 'Kertas mengkilap untuk brosur, flyer, dan pamflet.',
                'price_per_m2' => 8000.00, // Rp 8.000/m2 (atau per lembar)
                'stock' => 500,
                'is_active' => true,
            ],
            [
                'category_id' => $categoryId,
                'name' => 'Art Carton 260gr',
                'slug' => 'art-carton-260gr',
                'description' => 'Kertas tebal dan kaku untuk kartu nama.',
                'price_per_m2' => 12000.00, // Rp 12.000/m2
                'stock' => 300,
                'is_active' => true,
            ],
            [
                'category_id' => $categoryId,
                'name' => 'Stiker Vinyl',
                'slug' => 'stiker-vinyl',
                'description' => 'Stiker tahan air, cocok untuk label produk dan stiker outdoor.',
                'price_per_m2' => 35000.00, // Rp 35.000/m2
                'stock' => 40,
                'is_active' => true,
            ],
            [
                'category_id' => $categoryId,
                'name' => 'HVS 80gr',
                'slug' => 'hvs-80gr',
                'description' => 'Kertas standar untuk dokumen, nota, dan surat.',
                'price_per_m2' => 5000.00, // Rp 5.000/m2
                'stock' => 1000,
                'is_active' => true,
            ],
        ];

        foreach ($materials as $material) {
            Material::create($material);
        }

        $this->command->info('✅ Materials seeded successfully!');
    }
}