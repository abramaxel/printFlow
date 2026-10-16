<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Invoice (Tagihan yang dibayar)
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            
            // Relasi ke User (Siapa yang mengupload bukti pembayaran)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Detail Pembayaran
            $table->decimal('amount', 12, 2); // Nominal yang dibayar
            $table->string('payment_method'); // Metode: "Transfer BCA", "QRIS", "Dana", dll
            $table->string('proof_file')->nullable(); // Path file gambar bukti transfer (disimpan di storage)
            
            // Status & Verifikasi
            // Alur: pending -> verified (atau rejected)
            $table->string('status')->default('pending'); 
            $table->text('admin_notes')->nullable(); // Catatan admin jika bukti ditolak (misal: "Foto buram")
            $table->timestamp('verified_at')->nullable(); // Timestamp saat admin mengklik "Terima"
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};