<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah nomor kartu BPJS di tabel pasien
        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'bpjs_number')) {
                $table->string('bpjs_number')->nullable()->after('nik');
            }
        });

        // Tambah Cara Bayar di tabel antrean klinik
        Schema::table('clinic_queues', function (Blueprint $table) {
            if (!Schema::hasColumn('clinic_queues', 'payment_method')) {
                $table->string('payment_method')->default('Umum')->after('status')->comment('Umum / BPJS');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('bpjs_number');
        });
        Schema::table('clinic_queues', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};