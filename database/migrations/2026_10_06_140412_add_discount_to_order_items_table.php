<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Harga setelah diskon (Harga Deal)
            $table->decimal('final_subtotal', 12, 2)->default(0)->after('subtotal'); 
            // Nominal diskon yang diberikan
            $table->decimal('discount', 12, 2)->default(0)->after('final_subtotal'); 
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['final_subtotal', 'discount']);
        });
    }
};