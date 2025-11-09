<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <-- Pastikan ini di-import

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menambahkan ON UPDATE CURRENT_TIMESTAMP.
     */
    public function up(): void
    {
        // 1. Mengubah kolom 'registration_time' (SUDAH DIPERBAIKI)
        DB::statement("ALTER TABLE `clinic_queues` MODIFY `registration_time` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP");

        // 2. Mengubah kolom 'call_time' (Ini sudah benar)
        DB::statement("ALTER TABLE `clinic_queues` MODIFY `call_time` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP");
        
        // 3. Mengubah kolom 'finish_time' (Ini sudah benar)
        DB::statement("ALTER TABLE `clinic_queues` MODIFY `finish_time` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP");
    }

    /**
     * Batalkan migrasi, kembalikan kolom ke keadaan semula.
     */
    public function down(): void
    {
        // Fungsi down() penting untuk jika suatu saat Anda perlu membatalkan migrasi ini.
        
        // (SUDAH DIPERBAIKI)
        DB::statement("ALTER TABLE `clinic_queues` MODIFY `registration_time` TIMESTAMP NULL DEFAULT NULL");
        
        DB::statement("ALTER TABLE `clinic_queues` MODIFY `call_time` TIMESTAMP NULL DEFAULT NULL");
        DB::statement("ALTER TABLE `clinic_queues` MODIFY `finish_time` TIMESTAMP NULL DEFAULT NULL");
    }
};