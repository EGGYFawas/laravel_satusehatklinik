<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_queues', function (Blueprint $table) {
            $table->id(); // Sesuai ERD: bigIncrements PK
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('poli_id');
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('registered_by_user_id');
            $table->string('queue_number', 20);
            $table->text('chief_complaint');
            $table->enum('patient_relationship', ['Diri Sendiri', 'Anak', 'Orang Tua', 'Pasangan', 'Lainnya']);
            $table->string('patient_relationship_custom', 100)->nullable();
            $table->enum('status', ['MENUNGGU', 'DIPANGGIL', 'DIPERIKSA', 'SELESAI', 'BATAL']);
            $table->boolean('is_follow_up')->default(false);
            $table->text('follow_up_notes')->nullable();
            $table->timestamp('registration_time');
            $table->timestamp('call_time')->nullable();
            $table->timestamp('finish_time')->nullable();
            $table->timestamp('cancellation_time')->nullable();
            $table->enum('cancellation_actor', ['PASIEN', 'ADMIN', 'DOKTER'])->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            // Definisi relasi
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('poli_id')->references('id')->on('polis')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->foreign('registered_by_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_queues');
    }
};

