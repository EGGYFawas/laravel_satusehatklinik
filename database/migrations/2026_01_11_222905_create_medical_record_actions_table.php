<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('medical_record_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained('medical_records')->onDelete('cascade');
            $table->foreignId('medical_action_id')->constrained('medical_actions');
            
            // SNAPSHOT DATA (Penting untuk audit keuangan)
            // Jika harga master berubah bulan depan, riwayat transaksi hari ini harganya tetap
            $table->string('action_name'); 
            $table->decimal('price', 12, 2); 
            $table->string('result_notes')->nullable(); // Hasil: misal "200 mg/dL"

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_record_actions');
    }
};