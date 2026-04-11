<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clinic_settings', function (Blueprint $table) {
            // Menambahkan kolom gambar Mengapa Kami dan Deskripsi Tentang Kami
            $table->string('why_us_image')->nullable()->after('hero_image');
            $table->text('about_us_description')->nullable()->after('about_us_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinic_settings', function (Blueprint $table) {
            $table->dropColumn(['why_us_image', 'about_us_description']);
        });
    }
};