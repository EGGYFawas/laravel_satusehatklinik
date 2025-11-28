<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            // Menambahkan kolom untuk menyimpan ICD 10
            // Kita taruh setelah physical_examination_notes biar rapi
            $table->string('primary_icd10_code')->nullable()->after('physical_examination_notes');
            $table->string('primary_icd10_name')->nullable()->after('primary_icd10_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn(['primary_icd10_code', 'primary_icd10_name']);
        });
    }
};