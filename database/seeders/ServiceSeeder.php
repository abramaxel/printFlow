<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // ID 1 adalah kategori "Jenis Layanan" yang kita buat di CategorySeeder
        $categoryId = 1; 

        $services = [
            [
                'category_id' => $categoryId,
                'name' => 'Cetak Banner & Spanduk',
                'slug' => 'cetak-banner-spanduk',
                'description' => 'Layanan cetak banner dan spanduk outdoor/indoor. Harga dihitung per meter persegi (m2). Tersedia berbagai ukuran standar.',
                'price_per_m2' => 25000.00, // Rp 25.000/m2 (Harga estimasi pasar)
                'is_active' => true,
            ],
            [
                'category_id' => $categoryId,
                'name' => 'Cetak Brosur & Flyer',
                'slug' => 'cetak-brosur-flyer',
                'description' => 'Cetak brosur, flyer, dan pamflet untuk promosi. Menggunakan kertas Art Paper atau Matte Paper. Harga dasar per lembar.',
                'price_per_m2' => 1500.00, // Rp 1.500/lembar (Asumsi ukuran A5)
                'is_active' => true,
            ],
            [
                'category_id' => $categoryId,
                'name' => 'Cetak Kartu Nama',
                'slug' => 'cetak-kartu-nama',
                'description' => 'Cetak kartu nama profesional. Bahan Art Carton 260gr. Harga dasar per box (isi 100 lembar).',
                'price_per_m2' => 50000.00, // Rp 50.000/box
                'is_active' => true,
            ],
            [
                'category_id' => $categoryId,
                'name' => 'Cetak Stiker & Label',
                'slug' => 'cetak-stiker-label',
                'description' => 'Cetak stiker vinyl, chromo, atau transparan. Tahan air dan cocok untuk label produk. Harga per meter persegi.',
                'price_per_m2' => 45000.00, // Rp 45.000/m2
                'is_active' => true,
            ],
            [
                'category_id' => $categoryId,
                'name' => 'Cetak Poster & Pamflet',
                'slug' => 'cetak-poster-pamflet',
                'description' => 'Cetak poster ukuran besar (A3, A2, A1, A0) untuk keperluan akademik atau promosi. Harga per lembar.',
                'price_per_m2' => 15000.00, // Rp 15.000/lembar (Ukuran A2)
                'is_active' => true,
            ],
            [
                'category_id' => $categoryId,
                'name' => 'Cetak Undangan',
                'slug' => 'cetak-undangan',
                'description' => 'Cetak undangan pernikahan, khitanan, atau acara resmi. Custom desain dan bahan. Harga dasar per lembar.',
                'price_per_m2' => 3500.00, // Rp 3.500/lembar
                'is_active' => true,
            ],
            [
                'category_id' => $categoryId,
                'name' => 'Cetak Merchandise (Mug/ID Card)',
                'slug' => 'cetak-merchandise',
                'description' => 'Layanan cetak sablon digital untuk merchandise seperti Mug, ID Card, dan Pin. Harga per pcs.',
                'price_per_m2' => 35000.00, // Rp 35.000/pcs
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        $this->command->info('✅ Services seeded successfully!');
    }
}