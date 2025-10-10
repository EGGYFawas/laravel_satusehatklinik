<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Mengubah kolom enum 'status' untuk menambahkan nilai-nilai baru
        // Catatan: sintaks ini spesifik untuk MySQL.
        Schema::table('pharmacy_queues', function (Blueprint $table) {
            $table->enum('status', [
                'MENUNGGU_RACIK', 
                'SEDANG_DIRACIK', 
                'SIAP_DIAMBIL', 
                'SELESAI'
            ])->default('MENUNGGU_RACIK')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Mengembalikan ke status awal jika migrasi di-rollback
        Schema::table('pharmacy_queues', function (Blueprint $table) {
            $table->enum('status', [
                'MENUNGGU_RACIK'
            ])->default('MENUNGGU_RACIK')->change();
        });
    }
};