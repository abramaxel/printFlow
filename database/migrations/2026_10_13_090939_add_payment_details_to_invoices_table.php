<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'transfer'])->default('transfer')->after('status');
            $table->enum('payment_scheme', ['full', 'installment'])->default('full')->after('payment_method');
            $table->string('cash_receipt')->nullable()->after('payment_scheme'); // Untuk foto kuitansi tunai
            $table->text('installment_notes')->nullable()->after('cash_receipt'); // Untuk catatan cicilan (misal: "DP 50%, Cicilan ke-1 dari 3")
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_scheme', 'cash_receipt', 'installment_notes']);
        });
    }
};