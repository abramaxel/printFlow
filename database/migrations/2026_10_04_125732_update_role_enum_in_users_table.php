<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Mengubah enum role untuk menambahkan 'super_admin'
        DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(20)"); // Ubah ke varchar dulu agar aman di PostgreSQL
        DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'customer'");
    }

    public function down(): void
    {
        // Rollback jika perlu
    }
};