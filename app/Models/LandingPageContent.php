<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageContent extends Model
{
    use HasFactory;

    protected $table = 'landing_page_contents';

    protected $fillable = [
        'key',
        'label',
        'value', // Untuk Teks
        'image', // <--- PASTIKAN INI ADA (Untuk Gambar)
        'type',
    ];
}