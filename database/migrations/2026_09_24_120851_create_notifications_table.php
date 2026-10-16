<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            // Menggunakan UUID (Universally Unique Identifier) untuk ID notifikasi
            $table->uuid('id')->primary();
            
            // Tipe notifikasi (Misal: App\Notifications\OrderStatusUpdated)
            $table->string('type');
            
            // Siapa yang menerima notifikasi? (Bisa User, Admin, dll)
            $table->morphs('notifiable');
            
            // Isi pesan notifikasi (Disimpan dalam format JSON/Text)
            // Contoh: {"order_id": 12, "message": "Pesanan Anda sedang diproses", "url": "/orders/12"}
            $table->text('data');
            
            // Kapan notifikasi dibaca oleh user
            $table->timestamp('read_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};