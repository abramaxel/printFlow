<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Perbesar kolom di tabel 'invoices'
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->change();
            $table->decimal('shipping_cost', 15, 2)->change();
            $table->decimal('tax', 15, 2)->change();
            $table->decimal('discount', 15, 2)->change();
            $table->decimal('total_amount', 15, 2)->change();
            $table->decimal('total_paid', 15, 2)->change();
        });

        // 2. Perbesar kolom di tabel 'order_items'
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('unit_price_service', 15, 2)->change();
            $table->decimal('unit_price_material', 15, 2)->change();
            $table->decimal('subtotal', 15, 2)->change();
            $table->decimal('discount', 15, 2)->change();
            $table->decimal('final_subtotal', 15, 2)->change();
        });

        // 3. Perbesar kolom harga di tabel master (services & materials)
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('price_per_m2', 15, 2)->change();
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->decimal('price_per_m2', 15, 2)->change();
        });
    }

    public function down(): void
    {
        // Kembalikan ke ukuran lama jika terjadi kesalahan
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('subtotal', 12, 2)->change();
            $table->decimal('shipping_cost', 10, 2)->change();
            $table->decimal('tax', 10, 2)->change();
            $table->decimal('discount', 10, 2)->change();
            $table->decimal('total_amount', 12, 2)->change();
            $table->decimal('total_paid', 12, 2)->change();
        });
        
        // (Opsional: tambahkan down() untuk tabel lain jika perlu)
    }
};