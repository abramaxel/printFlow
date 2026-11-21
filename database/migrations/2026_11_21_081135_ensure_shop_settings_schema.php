<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Support\ShopSettings;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel belum ada sama sekali → buat模式 key-value.
        if (!Schema::hasTable('shop_settings')) {
            Schema::create('shop_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });

            return;
        }

        $cols = Schema::getColumnListing('shop_settings');

        // Sudah berbentuk key-value → tidak perlu kolom tambahan.
        if (in_array('key', $cols, true) && in_array('value', $cols, true)) {
            return;
        }

        // Mode kolom: tambahkan hanya field yang belum punya padanan.
        $textFields = ['shop_address', 'invoice_footer', 'payment_instructions'];
        $missingText = [];
        $missingString = [];

        foreach (ShopSettings::FIELDS as $canon => $aliases) {
            $exists = false;
            foreach ($aliases as $alias) {
                if (in_array($alias, $cols, true)) {
                    $exists = true;
                    break;
                }
            }
            if ($exists) {
                continue;
            }

            if (in_array($canon, $textFields, true)) {
                $missingText[] = $canon;
            } else {
                $missingString[] = $canon;
            }
        }

        if (empty($missingText) && empty($missingString)) {
            return;
        }

        Schema::table('shop_settings', function (Blueprint $table) use ($missingText, $missingString) {
            foreach ($missingString as $column) {
                $table->string($column)->nullable();
            }
            foreach ($missingText as $column) {
                $table->text($column)->nullable();
            }
        });
    }

    public function down(): void
    {
        // Konfigurasi toko dipakai bersama invoice & halaman pembayaran.
        // Sengaja tidak di-drop agar data rekening dan identitas tidak hilang.
    }
};