<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageContent extends Model
{
    use HasFactory;

    /**
     * Tabel yang terkait dengan model.
     * (Opsional jika nama tabel mengikuti konvensi Laravel jamak, tapi bagus untuk kejelasan)
     */
    protected $table = 'landing_page_contents';

    /**
     * Kolom-kolom yang boleh diisi secara massal (Mass Assignment).
     * Ini WAJIB ada agar Filament bisa menyimpan data.
     */
    protected $fillable = [
        'key',      // Kunci unik untuk pemanggilan di kodingan (misal: hero_title)
        'label',    // Label yang dibaca manusia (misal: Judul Utama)
        'value',    // Isi konten (teks atau path gambar)
        'type',     // Tipe input (text, textarea, image)
    ];

    /**
     * Tipe data kolom (Casting).
     * Berguna jika nanti 'value' menyimpan array/json, tapi saat ini string cukup.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}