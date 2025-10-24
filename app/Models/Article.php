<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    /**
     * Atribut-atribut yang harus di-cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime', // <-- INI PERBAIKAN ERROR 'translatedFormat on string'
        // Mungkin Anda punya cast lain di sini, biarkan saja
        // 'email_verified_at' => 'datetime', // (Contoh)
    ];

    /**
     * Atribut lain yang mungkin sudah ada di model Anda
     * (fillable, guarded, dll) biarkan saja.
     */
    protected $fillable = [
        'title',
        'content',
        'slug',
        'image_url',
        'published_at',
        'author_id', // Ditambahkan dari kode Anda
        // ... atribut lain
    ];

    /**
     * Mendapatkan data user (penulis) dari artikel ini.
     * (Dari kode yang Anda berikan)
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * [WAJIB ADA JIKA MENGGUNAKAN SLUG DI ROUTE]
     * Mengubah parameter binding default dari 'id' menjadi 'slug'.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}

