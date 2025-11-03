<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ini adalah "Pilar 3" dari optimasi.
     * Kita menambahkan "jalan tol" (index) ke database agar query AVG(TIMESTAMPDIFF)
     * dan WHERE 'created_at' berjalan super cepat.
     */
    public function up(): void
    {
        // Index untuk tabel antrean klinik
        Schema::table('clinic_queues', function (Blueprint $table) {
            $table->index('registration_time');
            $table->index('call_time');
            $table->index('finish_time');
            $table->index('created_at'); // Penting untuk filter "Hari Ini"
        });

        // Index untuk tabel antrean apotek
        Schema::table('pharmacy_queues', function (Blueprint $table) {
            $table->index('entry_time');
            $table->index('start_racik_time');
            $table->index('finish_racik_time');
            $table->index('taken_time');
            $table->index('created_at'); // Penting untuk filter "Hari Ini"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Logika untuk rollback jika diperlukan
        Schema::table('clinic_queues', function (Blueprint $table) {
            $table->dropIndex(['registration_time']);
            $table->dropIndex(['call_time']);
            $table->dropIndex(['finish_time']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('pharmacy_queues', function (Blueprint $table) {
            $table->dropIndex(['entry_time']);
            $table->dropIndex(['start_racik_time']);
            $table->dropIndex(['finish_racik_time']);
            $table->dropIndex(['taken_time']);
            $table->dropIndex(['created_at']);
        });
    }
};
