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
        Schema::create('medical_records', function (Blueprint $table) {
            // Membuat Primary Key `id` sebagai UNSIGNED BIGINT
            $table->id();

            // --- Definisi Eksplisit untuk Foreign Key ---

            // 1. Membuat kolom dengan tipe data yang 100% cocok dengan Primary Key induknya.
            $table->unsignedBigInteger('clinic_queue_id')->unique();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('doctor_id');

            // 2. Mendefinisikan relasi secara manual setelah kolom dibuat.
            $table->foreign('clinic_queue_id')->references('id')->on('clinic_queues')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
            
            // --- Akhir dari Definisi Eksplisit ---

            $table->dateTime('checkup_date');
            $table->text('doctor_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};

