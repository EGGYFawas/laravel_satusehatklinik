<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescription_details', function (Blueprint $table) {
            $table->id();

            // --- Perbaikan Eksplisit ---
            $table->unsignedBigInteger('prescription_id');
            $table->unsignedBigInteger('medicine_id');

            $table->foreign('prescription_id')->references('id')->on('prescriptions')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
            // --- Akhir Perbaikan ---

            $table->integer('quantity');
            $table->string('dosage', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_details');
    }
};
