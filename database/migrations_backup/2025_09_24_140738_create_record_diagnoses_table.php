<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('record_diagnoses', function (Blueprint $table) {
            $table->foreignId('medical_record_id')->constrained('medical_records')->onDelete('cascade');
            $table->foreignId('diagnosis_tag_id')->constrained('diagnosis_tags')->onDelete('cascade');
            $table->primary(['medical_record_id', 'diagnosis_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('record_diagnoses');
    }
};