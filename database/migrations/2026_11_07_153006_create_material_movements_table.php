<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            
            // Tipe: in, out, reserve, release, adjustment, transfer
            $table->string('movement_type', 20);
            // Sub: purchase_receipt, production_issue, reservation, rejection_release, opname_adjustment
            $table->string('sub_type', 50);
            
            $table->decimal('quantity', 15, 4);
            $table->string('unit', 20);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            
            // Referensi ke dokumen sumber
            $table->string('reference_type')->nullable(); // Order, Invoice, Payment, PurchaseRequest
            $table->unsignedBigInteger('reference_id')->nullable();
            
            // Batch tracking
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamp('movement_date')->useCurrent();
            $table->timestamps();
            
            $table->index(['material_id', 'movement_date']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('batch_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_movements');
    }
};