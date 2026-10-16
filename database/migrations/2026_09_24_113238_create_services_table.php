<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel categories (ID 1 = Jenis Layanan)
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            
            $table->string('name');           // Contoh: "Print Only", "Direct to Fabric"
            $table->string('slug')->unique(); // Contoh: "print-only"
            $table->text('description')->nullable(); // Penjelasan layanan
            $table->decimal('price_per_m2', 10, 2)->default(0); // Harga dasar per m2
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};