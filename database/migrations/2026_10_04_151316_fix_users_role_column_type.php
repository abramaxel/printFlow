<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus aturan (constraint) lama yang membatasi nilai role
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
        
        // 2. Ubah tipe data kolom 'role' menjadi VARCHAR(20) agar bisa menerima 'super_admin'
        // Perintah 'USING role::VARCHAR' memastikan data lama tetap aman dikonversi
        DB::statement('ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(20) USING role::VARCHAR');
        
        // 3. Pastikan nilai default-nya tetap 'customer'
        DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'customer'");
    }

    public function down(): void
    {
        // Tidak perlu rollback di tahap development ini
    }
};