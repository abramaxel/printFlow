<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menyiapkan skema BOM (Bill of Materials) untuk layanan cetak.
     * Bersifat idempoten: aman dijalankan meski tabel/kolom sudah ada.
     */
    public function up(): void
    {
        // ============================================================
        // 1. TABEL HEADER RESEP: service_boms
        // ============================================================
        if (!Schema::hasTable('service_boms')) {
            Schema::create('service_boms', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_id')->constrained()->cascadeOnDelete();
                $table->string('name')->nullable();
                $table->unsignedInteger('version')->default(1);
                $table->decimal('scrap_percentage', 5, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique('service_id');
            });
        } else {
            $bomCols = Schema::getColumnListing('service_boms');

            Schema::table('service_boms', function (Blueprint $table) use ($bomCols) {
                if (!in_array('name', $bomCols, true)) {
                    $table->string('name')->nullable();
                }
                if (!in_array('version', $bomCols, true)) {
                    $table->unsignedInteger('version')->default(1);
                }
                if (!in_array('is_active', $bomCols, true)) {
                    $table->boolean('is_active')->default(true);
                }
                if (!in_array('notes', $bomCols, true)) {
                    $table->text('notes')->nullable();
                }
                if (!in_array('scrap_percentage', $bomCols, true)) {
                    $table->decimal('scrap_percentage', 5, 2)->default(0);
                }
            });
        }

        // ============================================================
        // 2. TABEL DETAIL RESEP: service_bom_items
        // ============================================================
        if (!Schema::hasTable('service_bom_items')) {
            Schema::create('service_bom_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_bom_id')->constrained('service_boms')->cascadeOnDelete();
                $table->foreignId('material_id')->constrained()->restrictOnDelete();
                $table->decimal('quantity_per_unit', 15, 4)->default(1);
                $table->string('unit', 20)->default('m2');
                $table->decimal('scrap_percentage', 5, 2)->default(0);
                $table->boolean('is_optional')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['service_bom_id', 'sort_order']);
            });

            return;
        }

        // Tabel sudah ada — tambahkan hanya kolom yang benar-benar belum tersedia.
        $itemCols = Schema::getColumnListing('service_bom_items');

        // Deteksi nama kolom kebutuhan bahan yang mungkin sudah dipakai versi lama.
        $hasQuantityColumn = false;
        foreach (['quantity_per_unit', 'quantity_per_m2', 'quantity', 'usage_per_unit', 'qty_per_unit'] as $candidate) {
            if (in_array($candidate, $itemCols, true)) {
                $hasQuantityColumn = true;
                break;
            }
        }

        // Deteksi nama kolom scrap yang mungkin sudah dipakai versi lama.
        $hasScrapColumn = false;
        foreach (['scrap_percentage', 'scrap_allowance', 'scrap_percent', 'waste_percentage'] as $candidate) {
            if (in_array($candidate, $itemCols, true)) {
                $hasScrapColumn = true;
                break;
            }
        }

        Schema::table('service_bom_items', function (Blueprint $table) use ($itemCols, $hasQuantityColumn, $hasScrapColumn) {
            if (!$hasQuantityColumn) {
                $table->decimal('quantity_per_unit', 15, 4)->default(1);
            }
            if (!$hasScrapColumn) {
                $table->decimal('scrap_percentage', 5, 2)->default(0);
            }
            if (!in_array('unit', $itemCols, true)) {
                $table->string('unit', 20)->default('m2');
            }
            if (!in_array('is_optional', $itemCols, true)) {
                $table->boolean('is_optional')->default(false);
            }
            if (!in_array('sort_order', $itemCols, true)) {
                $table->unsignedInteger('sort_order')->default(0);
            }
            if (!in_array('notes', $itemCols, true)) {
                $table->text('notes')->nullable();
            }
        });
    }

    /**
     * Sengaja tidak melakukan drop.
     * Tabel BOM dipakai bersama modul MRP & produksi, sehingga rollback
     * tidak boleh menghapus resep yang sudah disusun tim produksi.
     */
    public function down(): void
    {
        //
    }
};