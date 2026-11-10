<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('material_movements', fn (Blueprint $t) =>
            $t->index(['material_id', 'movement_type', 'movement_date'], 'mm_mat_type_date_idx'));
        Schema::table('material_movements', fn (Blueprint $t) =>
            $t->index(['material_id', 'sub_type', 'movement_date'], 'mm_mat_sub_date_idx'));
        Schema::table('purchase_requests', fn (Blueprint $t) =>
            $t->index(['material_id', 'status'], 'pr_mat_status_idx'));
    }
    public function down(): void
    {
        Schema::table('material_movements', fn (Blueprint $t) =>
            $t->dropIndex('mm_mat_type_date_idx'));
        Schema::table('material_movements', fn (Blueprint $t) =>
            $t->dropIndex('mm_mat_sub_date_idx'));
        Schema::table('purchase_requests', fn (Blueprint $t) =>
            $t->dropIndex('pr_mat_status_idx'));
    }
};