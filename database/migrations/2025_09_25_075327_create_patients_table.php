<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id(); // Sesuai ERD: bigIncrements PK
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('nik', 16)->unique();
            // Kolom 'full_name' sengaja dihilangkan dari tabel ini.
            // Nama pasien akan diambil dari relasi ke tabel 'users' (users.full_name)
            // Ini adalah praktik normalisasi data untuk menghindari duplikasi.
            $table->date('date_of_birth');
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->text('address')->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->enum('blood_type', ['A', 'B', 'AB', 'O'])->nullable();
            $table->text('known_allergies')->nullable();
            $table->text('chronic_diseases')->nullable();
            $table->timestamps();

            // Definisi relasi
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};

