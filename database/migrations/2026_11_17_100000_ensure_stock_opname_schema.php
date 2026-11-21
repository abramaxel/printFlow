<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('stock_opnames')) {
            Schema::create('stock_opnames', function (Blueprint $table) {
                $table->id();
                $table->string('opname_number')->unique();
                $table->string('status', 20)->default('draft'); // draft, posted, cancelled
                $table->string('scope', 20)->default('manual'); // all, critical, category, manual
                $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->foreignId('counted_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('posted_at')->nullable();
                $table->unsignedInteger('items_count')->default(0);
                $table->unsignedInteger('variance_count')->default(0);
                $table->decimal('surplus_value', 15, 2)->default(0);
                $table->decimal('shrinkage_value', 15, 2)->default(0);
                $table->timestamps();
                $table->index(['status', 'created_at']);
            });
        }

        if (!Schema::hasTable('stock_opname_items')) {
            Schema::create('stock_opname_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('stock_opname_id')->constrained('stock_opnames')->cascadeOnDelete();
                $table->foreignId('material_id')->constrained()->restrictOnDelete();
                $table->decimal('system_qty', 15, 4)->default(0);
                $table->decimal('reserved_qty', 15, 4)->default(0);
                $table->decimal('physical_qty', 15, 4)->nullable();
                $table->decimal('variance_qty', 15, 4)->default(0);
                $table->decimal('unit_cost', 15, 2)->default(0);
                $table->decimal('variance_value', 15, 2)->default(0);
                $table->string('reason', 40)->nullable();
                $table->string('notes', 255)->nullable();
                $table->timestamps();
                $table->unique(['stock_opname_id', 'material_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
        Schema::dropIfExists('stock_opnames');
    }
};