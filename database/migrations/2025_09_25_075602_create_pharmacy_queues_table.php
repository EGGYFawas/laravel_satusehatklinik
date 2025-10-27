<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('pharmacy_queues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinic_queue_id')->unique();
            $table->unsignedBigInteger('prescription_id')->unique();
            $table->string('pharmacy_queue_number', 20);
            
            // [FINAL] Daftar status yang lengkap dan sesuai dengan alur
            $table->enum('status', [
                'MENUNGGU_RACIK', 
                'DIRACIK', 
                'SELESAI_RACIK', 
                'DIAMBIL', 
                'DITERIMA_PASIEN', // Status baru untuk konfirmasi pasien
                'BATAL'
            ]);

            // Kolom waktu untuk setiap tahapan
            $table->timestamp('entry_time');
            $table->timestamp('start_racik_time')->nullable();
            $table->timestamp('finish_racik_time')->nullable();
            $table->timestamp('taken_time')->nullable();
            // [BARU] Kolom waktu untuk menandai kapan pasien mengkonfirmasi
            $table->timestamp('patient_confirmed_time')->nullable(); 
            $table->timestamps();

            // Definisi relasi
            $table->foreign('clinic_queue_id')->references('id')->on('clinic_queues')->onDelete('cascade');
            $table->foreign('prescription_id')->references('id')->on('prescriptions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_queues');
    }
};