<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Perintah SQL native PostgreSQL untuk mengubah tipe data menjadi desimal (2 angka di belakang koma)
        // Ini TIDAK memerlukan doctrine/dbal sama sekali.
        DB::statement('ALTER TABLE materials ALTER COLUMN stock TYPE NUMERIC(15, 2)');
        DB::statement('ALTER TABLE materials ALTER COLUMN stock_reserved TYPE NUMERIC(15, 2)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke integer jika di-rollback
        DB::statement('ALTER TABLE materials ALTER COLUMN stock TYPE INTEGER');
        DB::statement('ALTER TABLE materials ALTER COLUMN stock_reserved TYPE INTEGER');
    }
};