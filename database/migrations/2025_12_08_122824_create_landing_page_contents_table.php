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
        Schema::create('landing_page_contents', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();        // Identitas unik (misal: hero_title)
            $table->string('label');                // Nama yang bisa dibaca Admin
            $table->longText('value')->nullable();  // Isi kontennya
            $table->string('type');                 // text, textarea, atau image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_contents');
    }
};

### Setelah file disimpan, jalankan perintah ini lagi di terminal: