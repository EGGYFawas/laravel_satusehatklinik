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
        Schema::table('clinic_queues', function (Blueprint $table) {
            // Mengubah kolom agar bisa NULL dan menghapus nilai DEFAULT
            
            // (SUDAH DIPERBAIKI)
            DB::statement("ALTER TABLE clinic_queues MODIFY COLUMN `registration_time` DATETIME NULL DEFAULT NULL");
            
            // (Ini sudah benar)
            DB::statement("ALTER TABLE clinic_queues MODIFY COLUMN `call_time` DATETIME NULL DEFAULT NULL");
            DB::statement("ALTER TABLE clinic_queues MODIFY COLUMN `finish_time` DATETIME NULL DEFAULT NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clinic_queues', function (Blueprint $table) {
            // (SUDAH DIPERBAIKI)
            $table->dateTime('registration_time')->nullable(false)->change();
            
            // (Ini sudah benar)
            $table->dateTime('call_time')->nullable(false)->change();
            $table->dateTime('finish_time')->nullable(false)->change();
        });
    }
};