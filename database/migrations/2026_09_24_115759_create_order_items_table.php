<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Header Order
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            
            // Relasi ke Master Data (Layanan, Bahan, Ukuran)
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('size_id')->nullable()->constrained()->nullOnDelete(); // Nullable karena banner tidak butuh ukuran S/M/L
            
            // Detail Kuantitas & Luas
            $table->integer('quantity')->default(1); // Jumlah barang (misal: 5 banner, 100 kartu nama)
            $table->decimal('area_m2', 8, 2)->default(0); // Total luas area dalam m2 (untuk kalkulasi harga)
            
            // Snapshot Harga (SANGAT PENTING!)
            // Kita simpan harga saat order dibuat, agar jika harga di master data berubah nanti, harga order lama tidak ikut berubah.
            $table->decimal('unit_price_service', 10, 2); // Harga layanan per m2/pcs saat order
            $table->decimal('unit_price_material', 10, 2); // Harga bahan per m2/pcs saat order
            $table->decimal('subtotal', 12, 2); // Total harga item ini (Quantity * Area * Harga)
            
            // File & Catatan
            $table->string('design_file')->nullable(); // Path file desain yang diupload (misal: designs/banner1.pdf)
            $table->text('notes')->nullable(); // Catatan khusus untuk item ini (misal: "Tolong warna merah lebih terang")
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};