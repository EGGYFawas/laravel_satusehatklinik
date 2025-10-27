<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Import Str jika akan menggunakan slug otomatis

class Article extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'author_id',
        'published_at',
        'image_url', // [PENAMBAHAN] Tambahkan image_url jika Anda menggunakannya
    ];

    /**
     * [PENAMBAHAN BARU]
     * The attributes that should be cast.
     * Ini akan mengubah 'published_at' menjadi objek Carbon secara otomatis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime', // Pastikan tipe data di DB = datetime/timestamp
    ];


    /**
     * Mendapatkan data user (penulis) dari artikel ini.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * [PENAMBAHAN BARU]
     * Menentukan kolom yang digunakan untuk Route Model Binding (jika pakai slug).
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * (Opsional) Jika Anda ingin slug dibuat otomatis saat judul diisi.
     */
    // protected static function boot()
    // {
    //     parent::boot();
    //     static::creating(function ($article) {
    //         if (empty($article->slug)) { // Hanya generate jika slug kosong
    //            $article->slug = Str::slug($article->title);
    //         }
    //     });
    //     static::updating(function ($article) {
    //          if ($article->isDirty('title') && empty($article->slug)) { // Hanya update jika slug kosong dan title berubah
    //              $article->slug = Str::slug($article->title);
    //          }
    //     });
    // }
}

