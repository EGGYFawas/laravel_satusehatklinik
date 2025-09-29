<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('poli_id')->constrained('polis')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->foreignId('registered_by_user_id')->constrained('users')->onDelete('cascade');
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
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_queues');
    }
};