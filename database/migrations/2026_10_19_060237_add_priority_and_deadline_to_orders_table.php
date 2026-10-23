<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Prioritas: normal, priority, express
            $table->enum('priority', ['normal', 'priority', 'express'])->default('normal')->after('status');
            // Deadline Pengerjaan (Tanggal Selesai)
            $table->date('deadline_date')->nullable()->after('priority');
        });
    }
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['priority', 'deadline_date']);
        });
    }
};
