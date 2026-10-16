<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke User (Siapa yang memesan)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Nomor Invoice Unik (Akan di-generate otomatis nanti, misal: INV-20240520-001)
            $table->string('invoice_number')->unique(); 
            
            // Status Pesanan (Menggunakan string agar fleksibel di PostgreSQL)
            // Alur: pending -> waiting_invoice -> unpaid -> paid -> processing -> qc -> shipping -> completed
            $table->string('status')->default('pending'); 
            
            // Metode Pengiriman
            $table->string('delivery_method')->default('pickup'); // pickup atau shipping
            
            // Alamat Pengiriman (Hanya diisi jika delivery_method = shipping)
            $table->text('shipping_address')->nullable(); 
            
            // Catatan tambahan dari pelanggan (Opsional)
            $table->text('notes')->nullable(); 

            $table->timestamps();
            $table->softDeletes(); // Fitur "Hapus Aman" (data tidak hilang permanen dari DB)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};