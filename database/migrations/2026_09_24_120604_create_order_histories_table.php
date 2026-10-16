<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_histories', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Order (Pesanan mana yang berubah statusnya)
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            
            // Relasi ke User (Siapa yang mengubah status? Admin/Kasir)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Status Sebelum dan Sesudah (Untuk keperluan log/audit)
            $table->string('old_status')->nullable(); // e.g., 'paid'
            $table->string('new_status');             // e.g., 'processing'
            
            // Catatan Tambahan
            $table->text('notes')->nullable(); 
            // Contoh catatan: "Bahan kain habis, mohon tunggu 2 hari" atau "Barang reject, cetak ulang"
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_histories');
    }
};