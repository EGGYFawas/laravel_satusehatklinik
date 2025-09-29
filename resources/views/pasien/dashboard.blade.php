<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pasien</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #3B82F6; /* Biru untuk tombol utama */
            --secondary-color: #A8FBD3; /* Hijau mint dari desain */
            --dark-purple: #4C51BF; /* Ungu gelap untuk tombol keluar */
            --text-dark: #333;
            --text-light: #555;
            --white-color: #FFFFFF;
            --bg-light: #F9FAFB;
            --card-bg: #FFFFFF;
            --font-family: 'Poppins', sans-serif;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-light);
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            background-color: var(--secondary-color);
            border: 2px solid #92eac5;
            border-radius: 12px;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header-greeting {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-greeting img {
            width: 50px;
            height: 50px;
        }

        .header-greeting h1 {
            font-size: 22px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .btn-logout {
            background-color: var(--dark-purple);
            color: var(--white-color);
            border: none;
            padding: 10px 25px;
            border-radius: 20px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-logout:hover {
            background-color: #3c409a;
        }

        /* Nav Menu */
        .nav-menu {
            display: flex;
            gap: 15px;
            justify-content: flex-start;
            margin-bottom: 30px;
        }

        .nav-button {
            background-color: #92eac5;
            color: var(--text-dark);
            border: none;
            padding: 12px 30px;
            border-radius: 20px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .nav-button:hover {
            background-color: #79d4b0;
        }

        /* Main Content */
        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .card {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .antrean-card {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .antrean-card img {
            max-width: 120px;
        }

        .antrean-card h2 {
            font-size: 24px;
            color: var(--primary-color);
            margin: 0 0 10px 0;
            font-weight: 600;
        }

        .antrean-card p {
            font-size: 15px;
            color: var(--text-light);
            margin: 0 0 20px 0;
        }

        .btn-primary {
            background-color: var(--dark-purple);
            color: var(--white-color);
            border: none;
            padding: 12px 30px;
            border-radius: 20px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: #3c409a;
        }
        
        .status-card {
            background-color: #eef2ff; /* Latar belakang lebih soft */
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .status-card h3 {
            font-size: 16px;
            font-weight: 500;
            color: var(--text-light);
            margin: 0 0 20px 0;
        }
        
        .status-card .queue-label {
            font-size: 18px;
            color: var(--text-dark);
            margin: 0 0 10px 0;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }

        .status-card .queue-number {
            font-size: 48px;
            font-weight: 700;
            color: var(--dark-purple);
        }

        /* Responsiveness */
        @media (max-width: 900px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            .header-greeting h1 {
                font-size: 18px;
            }
            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
         @media (max-width: 600px) {
            body { padding: 10px; }
            .header { flex-direction: column; gap: 15px; }
            .antrean-card { flex-direction: column; text-align: center; }
        }

    </style>
</head>
<body>

    <div class="container">
        <header class="header">
            <div class="header-greeting">
                <img src="{{ asset('assets/img/logo_login.png') }}" alt="Logo Klinik">
                {{-- DIUBAH: Menggunakan 'full_name' sesuai database --}}
                <h1>Selamat Datang, {{ Auth::user()->full_name }}</h1>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">Keluar</button>
            </form>
        </header>

        <nav class="nav-menu">
            <button class="nav-button">Dashboard</button>
            <button class="nav-button">Apotek</button>
            <button class="nav-button">Riwayat Kunjungan</button>
            <button class="nav-button">Daftar</button>
        </nav>

        <main class="main-content">
            <div class="card antrean-card">
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/online-doctor-consultation-5693986-4759529.png" alt="Ilustrasi Antrean Online">
                <div class="antrean-info">
                    <h2>Antrean Online</h2>
                    <p>Antrean Online, Lebih Efisien untuk Semua</p>
                    <button class="btn-primary">Ambil Antrean</button>
                </div>
            </div>

            <div class="card status-card">
                <h3>BELUM ADA ANTRIAN DIBUAT</h3>
                <div class="queue-label">Nomor Antrean</div>
                <div class="queue-number">-</div>
            </div>

            <div class="card antrean-card">
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/online-doctor-consultation-5693986-4759529.png" alt="Ilustrasi Antrean Online">
                <div class="antrean-info">
                    <h2>Antrean Online</h2>
                    <p>Antrean Online, Lebih Efisien untuk Semua</p>
                    <button class="btn-primary">Ambil Antrean</button>
                </div>
            </div>

            <div class="card status-card">
                <h3>BELUM ADA ANTRIAN DIBUAT</h3>
                <div class="queue-label">Nomor Antrean</div>
                <div class="queue-number">-</div>
            </div>
        </main>
    </div>

</body>
</html>
