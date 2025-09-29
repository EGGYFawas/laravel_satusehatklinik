<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('record_diagnoses', function (Blueprint $table) {
            $table->unsignedBigInteger('medical_record_id');
            $table->unsignedBigInteger('diagnosis_tag_id');
            
            // Definisi relasi
            $table->foreign('medical_record_id')->references('id')->on('medical_records')->onDelete('cascade');
            $table->foreign('diagnosis_tag_id')->references('id')->on('diagnosis_tags')->onDelete('cascade');

            // Definisi Primary Key gabungan
            $table->primary(['medical_record_id', 'diagnosis_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('record_diagnoses');
    }
};
