<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('contact_person')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->text('address')->nullable();
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('purchase_receipts')) {
            Schema::create('purchase_receipts', function (Blueprint $table) {
                $table->id();
                $table->string('bpb_number')->unique();
                $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
                $table->foreignId('purchase_request_id')->nullable()->constrained('purchase_requests')->nullOnDelete();
                $table->string('supplier_doc_number')->nullable(); // No. Surat Jalan supplier
                $table->date('receipt_date');
                $table->string('delivery_note_file')->nullable();  // scan surat jalan
                $table->string('invoice_file')->nullable();        // scan faktur supplier
                $table->decimal('total_cost', 15, 2)->default(0);
                $table->string('status', 20)->default('completed');
                $table->foreignId('received_by')->constrained('users')->restrictOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('purchase_receipt_items')) {
            Schema::create('purchase_receipt_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_receipt_id')->constrained()->cascadeOnDelete();
                $table->foreignId('material_id')->constrained()->restrictOnDelete();
                $table->decimal('quantity', 15, 4);
                $table->string('unit', 20);
                $table->decimal('unit_cost', 15, 2);
                $table->decimal('total_cost', 15, 2);
                $table->string('batch_number')->nullable();
                $table->date('expiry_date')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_receipt_items');
        Schema::dropIfExists('purchase_receipts');
        Schema::dropIfExists('suppliers');
    }
};