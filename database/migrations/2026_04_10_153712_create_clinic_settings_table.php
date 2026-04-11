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
        Schema::create('clinic_settings', function (Blueprint $table) {
            $table->id();
            
            // Identitas Klinik
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('logo')->nullable();

            // Tema & Warna (Custom SaaS)
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();

            // Landing Page Content
            $table->string('hero_title')->nullable();
            $table->string('hero_image')->nullable();
            $table->text('about_us_title')->nullable();
            $table->string('about_us_image')->nullable();

            // API Credentials (Encrypted)
            $table->text('midtrans_server_key')->nullable();
            $table->text('midtrans_client_key')->nullable();

            // SatuSehat
            $table->text('satusehat_client_id')->nullable();
            $table->text('satusehat_client_secret')->nullable();
            $table->text('satusehat_organization_id')->nullable();

            // BPJS (P-Care)
            $table->text('bpjs_cons_id')->nullable();
            $table->text('bpjs_secret_key')->nullable();
            $table->text('bpjs_user_key')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_settings');
    }
};