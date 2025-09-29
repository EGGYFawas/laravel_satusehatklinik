/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                // Sesuai permintaan Anda
                'brand-bg': '#E9E6E6',        // Background utama
                'brand-primary': '#24306E',   // Warna untuk button dan elemen penting
                'brand-text': '#FFF9F9',      // Warna teks di atas elemen primary
                
                // Warna tambahan dari desain landing page lama (opsional, tapi berguna)
                'text-dark': '#141414',
                'text-grey': '#646464',
            },
            fontFamily: {
                poppins: ['Poppins', 'sans-serif'],
            },
        },
    },
    plugins: [],
}

