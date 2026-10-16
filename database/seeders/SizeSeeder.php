<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Size;
use Illuminate\Support\Str;

class SizeSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = [
            // --- UKURAN KERTAS STANDAR (Umum) ---
            [
                'name' => 'A4 (21x29.7 cm)',
                'slug' => 'a4',
                'width_cm' => 21.00,
                'height_cm' => 29.70,
                'area_m2' => 0.0624, // (21 * 29.7) / 10000
                'price_adjustment' => 0.00,
                'is_active' => true,
            ],
            [
                'name' => 'A3 (29.7x42 cm)',
                'slug' => 'a3',
                'width_cm' => 29.70,
                'height_cm' => 42.00,
                'area_m2' => 0.1247,
                'price_adjustment' => 0.00,
                'is_active' => true,
            ],
            [
                'name' => 'A2 (42x59.4 cm)',
                'slug' => 'a2',
                'width_cm' => 42.00,
                'height_cm' => 59.40,
                'area_m2' => 0.2495,
                'price_adjustment' => 0.00,
                'is_active' => true,
            ],

            // --- UKURAN APPAREL (Sesuai Jurnal CV XYZ) ---
            // Digunakan khusus untuk layanan "FOB Printing Apparel"
            [
                'name' => 'Apparel Size XS',
                'slug' => 'apparel-xs',
                'width_cm' => null,
                'height_cm' => null,
                'area_m2' => 1.20, // Sesuai Tabel 2 Jurnal CV XYZ
                'price_adjustment' => 0.00,
                'is_active' => true,
            ],
            [
                'name' => 'Apparel Size S',
                'slug' => 'apparel-s',
                'width_cm' => null,
                'height_cm' => null,
                'area_m2' => 1.30, // Sesuai Tabel 2 Jurnal CV XYZ
                'price_adjustment' => 0.00,
                'is_active' => true,
            ],
            [
                'name' => 'Apparel Size M',
                'slug' => 'apparel-m',
                'width_cm' => null,
                'height_cm' => null,
                'area_m2' => 1.40, // Sesuai Tabel 2 Jurnal CV XYZ
                'price_adjustment' => 0.00,
                'is_active' => true,
            ],
            [
                'name' => 'Apparel Size L',
                'slug' => 'apparel-l',
                'width_cm' => null,
                'height_cm' => null,
                'area_m2' => 1.50, // Sesuai Tabel 2 Jurnal CV XYZ
                'price_adjustment' => 0.00,
                'is_active' => true,
            ],
            [
                'name' => 'Apparel Size XL',
                'slug' => 'apparel-xl',
                'width_cm' => null,
                'height_cm' => null,
                'area_m2' => 1.60, // Sesuai Tabel 2 Jurnal CV XYZ
                'price_adjustment' => 0.00,
                'is_active' => true,
            ],
            [
                'name' => 'Apparel Size XXL',
                'slug' => 'apparel-xxl',
                'width_cm' => null,
                'height_cm' => null,
                'area_m2' => 1.70, // Sesuai Tabel 2 Jurnal CV XYZ
                'price_adjustment' => 0.00,
                'is_active' => true,
            ],
        ];

        foreach ($sizes as $size) {
            Size::create($size);
        }

        $this->command->info('✅ Sizes seeded successfully!');
    }
}