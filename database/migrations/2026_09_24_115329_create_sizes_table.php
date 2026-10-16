<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // Contoh: "A4", "A3", "S", "M", "L", "Custom"
            $table->string('slug')->unique(); // Contoh: "a4", "size-s"
            $table->decimal('width_cm', 8, 2)->nullable();   // Lebar dalam cm (opsional)
            $table->decimal('height_cm', 8, 2)->nullable();  // Tinggi dalam cm (opsional)
            $table->decimal('area_m2', 8, 4)->default(0);    // Luas area dalam meter persegi (Sangat penting untuk rumus harga)
            $table->decimal('price_adjustment', 10, 2)->default(0); // Penyesuaian harga tambahan jika ada (opsional)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sizes');
    }
};