<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Riwayat Pergerakan Status (Order Histories)
        Schema::create('order_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Siapa yang mengubah status
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('notes')->nullable(); // e.g., "Barang reject, harus print ulang"
            $table->timestamps();
        });

        // 2. Notifikasi In-App
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable'); // Bisa untuk User atau Admin
            $table->text('data'); // Berisi pesan notifikasi (JSON)
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('order_histories');
    }
};