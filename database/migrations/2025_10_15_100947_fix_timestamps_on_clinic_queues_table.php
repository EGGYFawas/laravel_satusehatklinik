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
            // Perintah DB::statement digunakan untuk kompatibilitas yang lebih luas antar database
            DB::statement("ALTER TABLE clinic_queues MODIFY COLUMN `check_in_time` DATETIME NULL DEFAULT NULL");
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
            // Perintah ini mungkin tidak akan mengembalikan ke state yang sama persis
            // jika sebelumnya ada default value yang kompleks, tapi ini adalah rollback standar.
            $table->dateTime('check_in_time')->nullable(false)->change();
            $table->dateTime('call_time')->nullable(false)->change();
            $table->dateTime('finish_time')->nullable(false)->change();
        });
    }
};
