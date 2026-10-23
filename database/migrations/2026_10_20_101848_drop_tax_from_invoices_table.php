<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('invoices', 'tax')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('tax');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('invoices', 'tax')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->decimal('tax', 10, 2)->default(0)->after('shipping_cost');
            });
        }
    }
};