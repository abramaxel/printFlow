<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Jenis Layanan',
                'slug' => 'jenis-layanan',
                'description' => 'Kategori untuk jenis layanan printing (Print Only, Direct to Fabric, dll)',
                'is_active' => true,
            ],
            [
                'name' => 'Jenis Kain',
                'slug' => 'jenis-kain',
                'description' => 'Kategori untuk jenis bahan kain (Chiffon, Cotton, Satin, dll)',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('✅ Categories seeded successfully!');
    }
}