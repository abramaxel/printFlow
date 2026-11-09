<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number')->unique();
            $table->decimal('initial_quantity', 15, 4);
            $table->decimal('remaining_quantity', 15, 4);
            $table->decimal('unit_cost', 15, 2);
            $table->date('received_date');
            $table->date('expiry_date')->nullable();
            $table->string('supplier')->nullable();
            $table->enum('status', ['active', 'depleted', 'expired'])->default('active');
            $table->timestamps();
            
            $table->index(['material_id', 'status', 'expiry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_batches');
    }
};