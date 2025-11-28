<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Icd10 extends Model
{
    use HasFactory;

    protected $table = 'icd10s';

    // Kita izinkan mass assignment untuk kolom ini
    protected $fillable = [
        'code',
        'name',
    ];
}