<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
        if (in_array('key', $cols, true) && in_array('value', $cols, true)) return;

        Schema::table('shop_settings', function (Blueprint $table) use ($cols) {
            foreach (['qris_image', 'qris_merchant', 'ewallet_provider', 'ewallet_number', 'ewallet_holder'] as $c) {
                if (!in_array($c, $cols, true)) $table->string($c)->nullable();
            }
        });
    }

    public function down(): void
    {
        // Kanal pembayaran dipakai bersama halaman bayar — tidak di-drop.
    }
};