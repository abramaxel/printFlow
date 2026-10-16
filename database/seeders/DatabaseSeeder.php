<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         // Panggil seeder secara berurutan
        $this->call([
            CategorySeeder::class,  // 1. Isi kategori dulu
            ServiceSeeder::class,   // 2. Baru isi layanan
            // MaterialSeeder::class, // 3. Nanti kita tambahkan di sini
        ]);
    }
}
