<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel categories (ID 2 = Jenis Bahan/Kain)
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            
            $table->string('name');           // Contoh: "Flexi China", "Art Paper 150gr"
            $table->string('slug')->unique(); // Contoh: "flexi-china"
            $table->text('description')->nullable(); // Penjelasan bahan (ketebalan, jenis, dll)
            $table->decimal('price_per_m2', 10, 2)->default(0); // Harga dasar per m2/lembar
            $table->integer('stock')->default(0)->nullable(); // Stok bahan (opsional, bisa diisi 0 jika tidak dipakai)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};