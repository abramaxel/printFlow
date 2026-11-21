<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek apakah kolom sudah ada agar tidak error duplikat
        $addDetail = !Schema::hasColumn('users', 'address_detail');
        $addGuest  = !Schema::hasColumn('users', 'is_guest');

        if (!$addDetail && !$addGuest) {
            return; // Sudah ada, tidak perlu lakukan apa-apa
        }

        Schema::table('users', function (Blueprint $table) use ($addDetail, $addGuest) {
            if ($addDetail) {
                $table->string('address_detail', 500)->nullable();
            }
            if ($addGuest) {
                $table->boolean('is_guest')->default(false);
            }
        });
    }

    public function down(): void
    {
        $dropDetail = Schema::hasColumn('users', 'address_detail');
        $dropGuest  = Schema::hasColumn('users', 'is_guest');

        if (!$dropDetail && !$dropGuest) {
            return;
        }

        Schema::table('users', function (Blueprint $table) use ($dropDetail, $dropGuest) {
            if ($dropDetail) $table->dropColumn('address_detail');
            if ($dropGuest)  $table->dropColumn('is_guest');
        });
    }
};