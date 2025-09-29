<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id(); // Sesuai ERD: increments PK
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedBigInteger('poli_id');
            $table->string('specialization', 100);
            $table->string('license_number', 100)->unique();
            $table->timestamps();

            // Definisi relasi
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('poli_id')->references('id')->on('polis')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};

