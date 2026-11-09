<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            if (!Schema::hasColumn('materials', 'minimum_stock')) {
                $table->integer('minimum_stock')->default(0)->after('threshold');
            }
            if (!Schema::hasColumn('materials', 'maximum_stock')) {
                $table->integer('maximum_stock')->default(0)->after('minimum_stock');
            }
            if (!Schema::hasColumn('materials', 'lead_time_days')) {
                $table->integer('lead_time_days')->default(3)->after('maximum_stock');
            }
            if (!Schema::hasColumn('materials', 'ordering_cost')) {
                $table->decimal('ordering_cost', 15, 2)->default(50000)->after('lead_time_days');
            }
            if (!Schema::hasColumn('materials', 'holding_cost_rate')) {
                $table->decimal('holding_cost_rate', 5, 2)->default(0.20)->after('ordering_cost');
            }
            if (!Schema::hasColumn('materials', 'is_batch_tracked')) {
                $table->boolean('is_batch_tracked')->default(false)->after('holding_cost_rate');
            }
            if (!Schema::hasColumn('materials', 'shelf_life_days')) {
                $table->integer('shelf_life_days')->nullable()->after('is_batch_tracked');
            }
            if (!Schema::hasColumn('materials', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('shelf_life_days');
            }
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn([
                'minimum_stock', 'maximum_stock', 'lead_time_days',
                'ordering_cost', 'holding_cost_rate',
                'is_batch_tracked', 'shelf_life_days', 'is_active',
            ]);
        });
    }
};