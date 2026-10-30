<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('paid_at');
            }
            if (!Schema::hasColumn('invoices', 'cancelled_by')) {
                $table->foreignId('cancelled_by')->nullable()->after('cancelled_at')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('invoices', 'cancelled_reason')) {
                $table->text('cancelled_reason')->nullable()->after('cancelled_by');
            }
            if (!Schema::hasColumn('invoices', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('cancelled_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['cancelled_at', 'cancelled_by', 'cancelled_reason', 'is_locked']);
        });
    }
};