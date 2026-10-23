<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'duration_name')) {
                $table->string('duration_name')->nullable()->after('duration_multiplier');
            }
            if (!Schema::hasColumn('order_items', 'duration_days')) {
                $table->unsignedInteger('duration_days')->nullable()->after('duration_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            foreach (['duration_days', 'duration_name'] as $col) {
                if (Schema::hasColumn('order_items', $col)) $table->dropColumn($col);
            }
        });
    }
};