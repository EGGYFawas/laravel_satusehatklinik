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
        Schema::table('pharmacy_queues', function (Blueprint $table) {
            // Mengubah tipe kolom enum untuk menambahkan status baru 'DITERIMA_PASIEN'
            // dan menyelaraskan dengan alur yang sudah ada.
            $table->enum('status', [
                'MENUNGGU_RACIK', 
                'DIRACIK', 
                'SELESAI_RACIK', 
                'DIAMBIL', 
                'DITERIMA_PASIEN', // <-- Status baru ditambahkan di sini
                'BATAL'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('pharmacy_queues', function (Blueprint $table) {
            // Logika untuk mengembalikan perubahan jika migrasi di-rollback
            $table->enum('status', [
                'MENUNGGU_RACIK', 
                'DIRACIK', 
                'SELESAI_RACIK', 
                'DIAMBIL', 
                'BATAL'
            ])->change();
        });
    }
};
