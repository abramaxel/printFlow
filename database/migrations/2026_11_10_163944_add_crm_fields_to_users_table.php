<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'tier')) {
                $table->string('tier')->default('regular')->after('role'); // vip, regular, inactive
            }
            if (!Schema::hasColumn('users', 'internal_notes')) {
                $table->text('internal_notes')->nullable()->after('tier');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['tier', 'internal_notes']);
        });
    }
};