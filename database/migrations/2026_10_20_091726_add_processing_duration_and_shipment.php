<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ===== TABEL MASTER: Paket Durasi Pengerjaan (pilihan pelanggan) =====
        if (!Schema::hasTable('processing_durations')) {
            Schema::create('processing_durations', function (Blueprint $table) {
                $table->id();
                $table->string('name');                 // Normal / Prioritas / Express
                $table->string('slug')->unique();       // normal / priority / express
                $table->string('description')->nullable();
                $table->integer('days')->default(3);    // SLA hari kerja
                $table->decimal('multiplier', 5, 2)->default(1.00); // pengali harga
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // ===== ORDER_ITEMS: ikatan durasi + snapshot harga durasi =====
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'duration_id')) {
                $table->foreignId('duration_id')
                      ->nullable()
                      ->constrained('processing_durations')
                      ->nullOnDelete()
                      ->after('size_id');
            }
            if (!Schema::hasColumn('order_items', 'duration_multiplier')) {
                $table->decimal('duration_multiplier', 5, 2)->default(1.00)->after('duration_id');
            }
            if (!Schema::hasColumn('order_items', 'duration_fee')) {
                // selisih harga akibat memilih durasi lebih cepat (untuk laporan upsell)
                $table->decimal('duration_fee', 15, 2)->default(0)->after('discount');
            }
        });

        // ===== ORDERS: metode kirim + deadline otomatis =====
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'shipping_method')) {
                $table->string('shipping_method')->default('pickup')->after('status');
            }
            if (!Schema::hasColumn('orders', 'shipping_address')) {
                $table->text('shipping_address')->nullable()->after('shipping_method');
            }
            if (!Schema::hasColumn('orders', 'deadline_date')) {
                $table->date('deadline_date')->nullable()->after('shipping_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            foreach (['deadline_date', 'shipping_address', 'shipping_method'] as $col) {
                if (Schema::hasColumn('orders', $col)) $table->dropColumn($col);
            }
        });
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'duration_id')) {
                $table->dropConstrainedForeignId('duration_id');
            }
            foreach (['duration_fee', 'duration_multiplier'] as $col) {
                if (Schema::hasColumn('order_items', $col)) $table->dropColumn($col);
            }
        });
        Schema::dropIfExists('processing_durations');
    }
};