<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinic_queues', function (Blueprint $table) {
            // Tambahkan kolom baru setelah 'registration_time'
            $table->timestamp('check_in_time')->nullable()->after('registration_time');
        });
        
        // Modifikasi kolom ENUM untuk menambahkan status 'HADIR'
        // Raw statement diperlukan untuk kompatibilitas cross-database
        DB::statement("ALTER TABLE clinic_queues MODIFY COLUMN status ENUM('MENUNGGU', 'HADIR', 'DIPANGGIL', 'DIPERIKSA', 'SELESAI', 'BATAL') NOT NULL DEFAULT 'MENUNGGU'");
    }

    public function down(): void
    {
        Schema::table('clinic_queues', function (Blueprint $table) {
            $table->dropColumn('check_in_time');
        });
        
        // Kembalikan ENUM ke kondisi semula jika rollback
        DB::statement("ALTER TABLE clinic_queues MODIFY COLUMN status ENUM('MENUNGGU', 'DIPANGGIL', 'DIPERIKSA', 'SELESAI', 'BATAL') NOT NULL DEFAULT 'MENUNGGU'");
    }
};
