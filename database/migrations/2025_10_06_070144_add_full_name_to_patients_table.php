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
    public function up()
    {
        // Perintah untuk memodifikasi tabel 'patients'
        Schema::table('patients', function (Blueprint $table) {
            // Cek jika kolom 'full_name' belum ada untuk menghindari error jika migrasi dijalankan ulang
            if (!Schema::hasColumn('patients', 'full_name')) {
                // Tambahkan kolom 'full_name' setelah kolom 'nik'
                // Kolom ini dibuat nullable untuk sementara agar tidak error pada data lama yang mungkin tidak memiliki nama
                // Anda bisa mengubahnya menjadi not nullable jika semua data sudah dipastikan terisi
                $table->string('full_name', 255)->after('nik')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            // Perintah untuk menghapus kolom jika migrasi di-rollback
            if (Schema::hasColumn('patients', 'full_name')) {
                $table->dropColumn('full_name');
            }
        });
    }
};