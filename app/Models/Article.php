<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    /**
     * Mendapatkan data user (penulis) dari artikel ini.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}

