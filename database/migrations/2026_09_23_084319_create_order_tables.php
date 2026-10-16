<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Header Pesanan (Orders)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique(); // e.g., INV-20240520-001
            $table->enum('status', [
                'pending', 'waiting_invoice', 'unpaid', 'paid', 
                'processing', 'qc', 'shipping', 'completed', 'cancelled', 'rejected'
            ])->default('pending');
            $table->enum('delivery_method', ['pickup', 'shipping'])->default('pickup');
            $table->text('shipping_address')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Untuk fitur hapus aman
        });

        // 2. Detail Pesanan (Order Items)
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('size_id')->nullable()->constrained()->nullOnDelete(); // Null jika bukan FOB Apparel
            
            $table->integer('quantity')->default(1);
            $table->decimal('area_m2', 8, 2)->default(0); // Total luas area
            $table->decimal('unit_price_service', 10, 2); // Harga layanan saat order
            $table->decimal('unit_price_material', 10, 2); // Harga bahan saat order
            $table->decimal('subtotal', 12, 2); // (Area * Qty * Harga)
            
            $table->string('design_file')->nullable(); // Path file desain yang diupload
            $table->text('notes')->nullable(); // Catatan khusus produksi
            $table->timestamps();
        });

        // 3. Tagihan (Invoices)
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->decimal('subtotal', 12, 2); // Total dari order_items
            $table->decimal('shipping_cost', 10, 2)->default(0); // Ongkos kirim
            $table->decimal('tax', 10, 2)->default(0); // PPN (misal 11%)
            $table->decimal('total_amount', 12, 2); // Grand total
            $table->enum('status', ['unpaid', 'paid', 'overdue'])->default('unpaid');
            $table->timestamp('due_date')->nullable();
            $table->timestamps();
        });

        // 4. Pembayaran (Payments)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // User yang upload bukti
            $table->decimal('amount', 12, 2);
            $table->string('payment_method'); // e.g., "Bank Transfer BCA", "GoPay"
            $table->string('proof_file')->nullable(); // Path foto bukti transfer
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable(); // Catatan admin jika ditolak
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};