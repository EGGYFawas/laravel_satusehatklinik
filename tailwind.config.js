/** @type {import('tailwindcss').Config} */

// === AWAL MODIFIKASI (Impor Preset & Forms) ===
import preset from './vendor/filament/filament/tailwind.config.js'; // Ini sudah benar
import typography from '@tailwindcss/typography'; // <-- 1. TAMBAHKAN IMPORT INI
import forms from '@tailwindcss/forms'; // Ini WAJIB
// === AKHIR MODIFIKASI ===

export default {
    // === AWAL MODIFIKASI (Tambahkan Preset) ===
    presets: [preset],
    // === AKHIR MODIFIKASI ===

    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",

        // === AWAL MODIFIKASI (Path Konten Filament) ===
        // Path ini WAJIB ada agar Tailwind men-scan file Filament
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        // === AKHIR MODIFIKASI ===
    ],
    theme: {
        extend: {
            colors: {
                // Warna kustom 3 role-mu (AMAN)
                'brand-bg': '#E9E6E6',
                'brand-primary': '#24306E',
                'brand-text': '#FFF9F9',
                'text-dark': '#141414',
                'text-grey': '#646464',
            },
            fontFamily: {
                poppins: ['Poppins', 'sans-serif'],
            },
        },
    },
    // === AWAL MODIFIKASI (Tambahkan Plugin) ===
    plugins: [
        forms, // Plugin @tailwindcss/forms WAJIB
        typography,
    ],
    // === AKHIR MODIFIKASI ===
}
