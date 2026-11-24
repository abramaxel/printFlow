<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('email_verification_otps')) {
            Schema::create('email_verification_otps', function (Blueprint $table) {
                $table->id();
                $table->string('email', 190);
                $table->text('payload');
                $table->string('otp_hash');
                $table->unsignedTinyInteger('attempts')->default(0);
                $table->timestamp('expires_at');
                $table->timestamp('verified_at')->nullable();
                $table->timestamp('consumed_at')->nullable();
                $table->string('ip', 64)->nullable();
                $table->timestamps();
                $table->index(['email', 'created_at']);
            });
        }

        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'company_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('company_name', 150)->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('email_verification_otps');
    }
};