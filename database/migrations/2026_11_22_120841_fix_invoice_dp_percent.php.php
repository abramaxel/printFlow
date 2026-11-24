<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('invoices')) return;

        $cols = Schema::getColumnListing('invoices');

        Schema::table('invoices', function (Blueprint $table) use ($cols) {
            if (!in_array('payment_scheme', $cols, true)) {
                $table->string('payment_scheme', 20)->default('full');
            }
            if (!in_array('dp_percent', $cols, true)) {
                $table->decimal('dp_percent', 5, 2)->default(0);
            }
        });

        try {
            DB::table('invoices')
                ->where('payment_scheme', 'installment')
                ->where(function ($q) {
                    $q->whereNull('dp_percent')->orWhere('dp_percent', '<=', 0);
                })
                ->update(['dp_percent' => 50]);
        } catch (\Throwable $e) {
        }
    }

    public function down(): void
    {
    }
};