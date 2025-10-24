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
        // Menggunakan Schema::table() untuk memodifikasi tabel yang sudah ada
        Schema::table('medical_records', function (Blueprint $table) {
            // Menambahkan kolom-kolom baru setelah kolom 'doctor_notes'
            $table->string('blood_pressure')->nullable()->after('doctor_notes')->comment('Contoh: 120/80');
            $table->integer('heart_rate')->nullable()->after('blood_pressure')->comment('Denyut per menit');
            $table->integer('respiratory_rate')->nullable()->after('heart_rate')->comment('Napas per menit');
            $table->decimal('temperature', 4, 1)->nullable()->after('respiratory_rate')->comment('Dalam derajat Celsius');
            $table->integer('oxygen_saturation')->nullable()->after('temperature')->comment('Dalam persen');
            $table->text('physical_examination_notes')->nullable()->after('oxygen_saturation')->comment('Catatan temuan fisik lainnya');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Logika untuk membatalkan (rollback) migrasi
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn([
                'blood_pressure',
                'heart_rate',
                'respiratory_rate',
                'temperature',
                'oxygen_saturation',
                'physical_examination_notes'
            ]);
        });
    }
};

