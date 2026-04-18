<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $author = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->first();

        if ($author) {
            $title = '5 Tips Menjaga Kesehatan Jantung Anda';
            Article::firstOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'content' => 'Berikut adalah lima tips sederhana untuk menjaga kesehatan jantung...',
                    'author_id' => $author->id,
                    'published_at' => now(),
                ]
            );
        }
    }
}
