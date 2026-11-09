<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_bom_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_bom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->restrictOnDelete();
            $table->decimal('quantity_per_unit', 15, 4); // kebutuhan per m2
            $table->string('unit'); // m2, liter, pcs
            $table->decimal('scrap_percentage', 5, 2)->default(0); // toleransi waste
            $table->boolean('is_primary')->default(false); // bahan utama vs pendukung
            $table->integer('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['service_bom_id', 'material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_bom_items');
    }
};