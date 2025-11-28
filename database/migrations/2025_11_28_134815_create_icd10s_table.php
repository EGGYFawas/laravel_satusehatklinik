<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('icd10s', function (Blueprint $table) {
            $table->id();
            // Code biasanya pendek (A00.1), kita kasih index biar searching cepet
            $table->string('code')->index(); 
            // Nama penyakit bisa panjang
            $table->text('name'); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('icd10s');
    }
};