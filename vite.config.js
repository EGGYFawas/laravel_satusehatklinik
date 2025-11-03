import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // === [PENGHAPUSAN STRATEGI B] ===
            // Kita HANYA perlu 'app.css' dan 'app.js'
            // 'tailwind.config.js'-mu akan otomatis
            // memasukkan style Filament ke 'app.css'.
            input: [
                'resources/css/app.css', // CSS Tailwind utamamu
                'resources/js/app.js',   // JS utamamu
            ],
            // === AKHIR PENGHAPUSAN ===
            refresh: true,
        }),
    ],
});