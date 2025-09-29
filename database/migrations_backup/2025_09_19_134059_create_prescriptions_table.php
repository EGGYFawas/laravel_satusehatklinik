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
        Schema::create('prescriptions', function (Blueprint $table) {
            // Membuat Primary Key `id` sebagai UNSIGNED BIGINT
            $table->id();

            // --- PERBAIKAN EKSPLISIT DIMULAI DI SINI ---

            // 1. Definisikan kolom foreign key dengan tipe data yang 100% identik.
            $table->unsignedBigInteger('medical_record_id')->unique();

            // 2. Setelah kolom dibuat, definisikan relasinya secara manual.
            $table->foreign('medical_record_id')->references('id')->on('medical_records')->onDelete('cascade');

            // --- AKHIR DARI PERBAIKAN ---

            $table->dateTime('prescription_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
