<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pharmacy_queues', function (Blueprint $table) {
            $table->id(); // Sesuai ERD: bigIncrements PK
            $table->unsignedBigInteger('clinic_queue_id')->unique();
            $table->unsignedBigInteger('prescription_id')->unique();
            $table->string('pharmacy_queue_number', 20);
            $table->enum('status', ['MENUNGGU_RACIK', 'DIRACIK', 'SELESAI_RACIK', 'DIAMBIL', 'BATAL']);
            $table->timestamp('entry_time');
            $table->timestamp('start_racik_time')->nullable();
            $table->timestamp('finish_racik_time')->nullable();
            $table->timestamp('taken_time')->nullable();
            $table->timestamps();

            // Definisi relasi
            $table->foreign('clinic_queue_id')->references('id')->on('clinic_queues')->onDelete('cascade');
            $table->foreign('prescription_id')->references('id')->on('prescriptions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacy_queues');
    }
};

