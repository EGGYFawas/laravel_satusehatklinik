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
                "resources/css/app.css", // CSS Tailwind utamamu
                "resources/js/app.js", // JS utamamu

                // CSS Inti Filament (ini akan di-bundle oleh Vite)
                "vendor/filament/forms/resources/css/forms.css",
                "vendor/filament/tables/resources/css/tables.css",
                "vendor/filament/notifications/resources/css/notifications.css",
                "vendor/filament/support/resources/css/support.css",
            ],
            // === AKHIR PENGHAPUSAN ===
            refresh: true,
        }),
    ],
    server: {
        host: '127.0.0.1',
    },
});
