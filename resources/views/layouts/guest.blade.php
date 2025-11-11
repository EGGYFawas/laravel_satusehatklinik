<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    {{-- Judul halaman akan dinamis, defaultnya 'Klinik Sehat' --}}
    <title>@yield('title', 'Klinik Sehat')</title>

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Vite/Tailwind CSS -->
    {{-- Baris ini akan memanggil CSS yang dihasilkan oleh 'npm run dev' --}}
    @vite('resources/css/app.css')
</head>
<body class="font-poppins bg-brand-bg text-text-dark antialiased">

    {{-- 
      Ini adalah 'area kosong' di dalam cetakan.
      Konten dari file lain (seperti login.blade.php atau landing.blade.php) 
      yang menggunakan @section('content') akan dimasukkan di sini.
    --}}
    @yield('content')

</body>
</html>

