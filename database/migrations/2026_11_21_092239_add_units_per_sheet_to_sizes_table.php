<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sizes')) return;

        $cols = Schema::getColumnListing('sizes');

        $unitCol = null;
        foreach (['units_per_sheet', 'pcs_per_sheet', 'units_per_unit', 'qty_per_unit'] as $c) {
            if (in_array($c, $cols, true)) { $unitCol = $c; break; }
        }

        if (!$unitCol) {
            Schema::table('sizes', function (Blueprint $table) {
                $table->unsignedInteger('units_per_sheet')->default(1);
            });
            $unitCol = 'units_per_sheet';
            $cols[] = 'units_per_sheet';
        }

        if (!in_array('width_cm', $cols, true) || !in_array('height_cm', $cols, true) || !in_array('area_m2', $cols, true)) {
            return;
        }

        // Backfill konservatif: hanya bila luas tersimpan merupakan kelipatan
        // bulat yang sangat presisi dari luas satu pcs (toleransi 0,2%).
        foreach (DB::table('sizes')->get(['id', 'width_cm', 'height_cm', 'area_m2', $unitCol]) as $r) {
            if ((int) ($r->{$unitCol} ?? 1) > 1) continue;

            $w = (float) ($r->width_cm ?? 0);
            $h = (float) ($r->height_cm ?? 0);
            $stored = (float) ($r->area_m2 ?? 0);
            if ($w <= 0 || $h <= 0 || $stored <= 0) continue;

            $single = ($w * $h) / 10000;
            if ($single <= 0) continue;

            $ratio = $stored / $single;
            if ($ratio < 1.98) continue;

            $nearest = (int) round($ratio);
            if ($nearest < 2) continue;
            if (abs($ratio - $nearest) / $nearest > 0.002) continue;

            DB::table('sizes')->where('id', $r->id)->update([$unitCol => $nearest]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sizes') && Schema::hasColumn('sizes', 'units_per_sheet')) {
            Schema::table('sizes', function (Blueprint $table) {
                $table->dropColumn('units_per_sheet');
            });
        }
    }
};