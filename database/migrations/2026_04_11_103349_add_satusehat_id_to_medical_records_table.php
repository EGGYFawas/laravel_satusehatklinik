<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            // Menyimpan ID rujukan dari Kemenkes jika sukses terkirim
            $table->string('satusehat_encounter_id')->nullable()->after('primary_icd10_name');
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn('satusehat_encounter_id');
        });
    }
};