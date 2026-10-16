<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel untuk kategori (Jenis Layanan, Jenis Kain, dll)
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // Contoh: "Jenis Layanan", "Jenis Kain"
            $table->string('slug')->unique(); // Contoh: "jenis-layanan", "jenis-kain"
            $table->text('description')->nullable(); // Deskripsi kategori
            $table->boolean('is_active')->default(true); // Status aktif/tidak
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};