<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Daftar status BARU
        $newStatuses = "'DALAM_ANTREAN', 'SEDANG_DIRACIK', 'SIAP_DIAMBIL', 'DISERAHKAN', 'DITERIMA_PASIEN', 'BATAL'";

        // [MODIFIKASI UTAMA] Menggunakan proses 3 langkah yang aman

        // Langkah 1: Ubah kolom ke VARCHAR untuk sementara agar bisa menerima nilai baru
        DB::statement("ALTER TABLE `pharmacy_queues` MODIFY `status` VARCHAR(255)");

        // Langkah 2: Lakukan update data setelah kolomnya fleksibel
        DB::table('pharmacy_queues')->where('status', 'MENUNGGU_RACIK')->update(['status' => 'DALAM_ANTREAN']);
        DB::table('pharmacy_queues')->where('status', 'DIRACIK')->update(['status' => 'SEDANG_DIRACIK']);
        DB::table('pharmacy_queues')->where('status', 'SELESAI_RACIK')->update(['status' => 'SIAP_DIAMBIL']);
        DB::table('pharmacy_queues')->where('status', 'DIAMBIL')->update(['status' => 'DISERAHKAN']);

        // Langkah 3: Ubah kembali ke ENUM dengan definisi baru dan set default value
        DB::statement("ALTER TABLE `pharmacy_queues` MODIFY `status` ENUM({$newStatuses}) NOT NULL DEFAULT 'DALAM_ANTREAN'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Daftar status LAMA
        $oldStatuses = "'MENUNGGU_RACIK', 'DIRACIK', 'SELESAI_RACIK', 'DIAMBIL', 'BATAL'";

        // Lakukan proses 3 langkah secara terbalik untuk rollback

        // Langkah 1: Ubah kolom ke VARCHAR untuk sementara
        DB::statement("ALTER TABLE `pharmacy_queues` MODIFY `status` VARCHAR(255)");

        // Langkah 2: Ubah data kembali ke format lama
        DB::table('pharmacy_queues')->where('status', 'DALAM_ANTREAN')->update(['status' => 'MENUNGGU_RACIK']);
        DB::table('pharmacy_queues')->where('status', 'SEDANG_DIRACIK')->update(['status' => 'DIRACIK']);
        DB::table('pharmacy_queues')->where('status', 'SIAP_DIAMBIL')->update(['status' => 'SELESAI_RACIK']);
        DB::table('pharmacy_queues')->where('status', 'DISERAHKAN')->update(['status' => 'DIAMBIL']);
        DB::table('pharmacy_queues')->where('status', 'DITERIMA_PASIEN')->update(['status' => 'DIAMBIL']);
        
        // Langkah 3: Ubah kembali ke ENUM dengan definisi lama
        DB::statement("ALTER TABLE `pharmacy_queues` MODIFY `status` ENUM({$oldStatuses})");
    }
};
    

