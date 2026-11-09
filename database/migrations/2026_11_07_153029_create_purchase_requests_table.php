<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('pr_number')->unique(); // PR-2026-0001
            $table->foreignId('material_id')->constrained()->restrictOnDelete();
            $table->decimal('requested_quantity', 15, 4);
            $table->string('unit', 20);
            $table->decimal('estimated_cost', 15, 2);
            $table->string('urgency', 20)->default('normal'); // critical, warning, normal
            $table->string('reason')->nullable(); // 'ROP trigger', 'MRP calculation'
            $table->string('status', 20)->default('pending'); // pending, approved, ordered, received, cancelled
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->date('needed_by_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'urgency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};