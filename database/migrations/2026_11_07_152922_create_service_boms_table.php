<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_boms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique(); // BOM-SPNDK, BOM-BNR
            $table->string('name');
            $table->string('base_unit')->default('m2'); // satuan output
            $table->boolean('is_active')->default(true);
            $table->integer('version')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_boms');
    }
};