<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed data default
        $defaults = [
            'shop_name'     => 'PrintOrder',
            'shop_address'  => 'Jl. Percetakan No. 1, Jakarta',
            'shop_phone'    => '0812-3456-7890',
            'shop_email'    => 'info@printorder.test',
            'bank_name'     => 'BCA',
            'bank_account'  => '1234567890',
            'bank_holder'   => 'PT Print Order Indonesia',
            'invoice_footer'=> 'Terima kasih atas kepercayaan Anda!',
        ];

        foreach ($defaults as $key => $value) {
            \App\Models\ShopSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_settings');
    }
};