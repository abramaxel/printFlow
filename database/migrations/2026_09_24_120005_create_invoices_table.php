<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            
            // Relasi One-to-One ke Orders (Satu pesanan punya satu tagihan resmi)
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            
            // Rincian Biaya (Snapshot saat tagihan dibuat)
            $table->decimal('subtotal', 12, 2)->default(0); // Total dari semua order_items
            $table->decimal('shipping_cost', 10, 2)->default(0); // Ongkos kirim (diinput admin)
            $table->decimal('tax', 10, 2)->default(0); // PPN (misal 11%, bisa 0 jika tidak kena pajak)
            $table->decimal('discount', 10, 2)->default(0); // Diskon (opsional, untuk promo)
            $table->decimal('total_amount', 12, 2); // Grand Total (Subtotal + Shipping + Tax - Discount)
            
            // Status & Waktu
            // Alur: unpaid -> paid -> (atau overdue jika telat bayar)
            $table->string('status')->default('unpaid'); 
            $table->timestamp('due_date')->nullable(); // Batas waktu pembayaran
            $table->timestamp('paid_at')->nullable(); // Kapan pelanggan benar-benar membayar
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};