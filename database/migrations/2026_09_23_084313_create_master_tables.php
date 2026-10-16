<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Kategori (Untuk mengelompokkan Layanan dan Bahan)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Jenis Layanan", "Jenis Kain"
            $table->string('slug')->unique(); // e.g., "jenis-layanan"
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Layanan (Services)
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "Direct to Fabric", "Sublimation", "FOB Apparel"
            $table->string('slug')->unique();
            $table->decimal('price_per_m2', 10, 2)->default(0); // Harga dasar per m2
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Tabel Bahan Kain (Materials)
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "Chiffon", "Cotton Tencel"
            $table->string('slug')->unique();
            $table->decimal('price_per_m2', 10, 2)->default(0);
            $table->integer('stock')->default(0); // Stok dalam meter
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Tabel Ukuran (Khusus untuk layanan FOB Apparel)
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "XS", "S", "M", "L", "XL"
            $table->decimal('area_m2', 5, 2); // Luas area dalam m2 (e.g., 1.20)
            $table->decimal('price_adjustment', 10, 2)->default(0); // Penyesuaian harga jika ada
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sizes');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('services');
        Schema::dropIfExists('categories');
    }
};