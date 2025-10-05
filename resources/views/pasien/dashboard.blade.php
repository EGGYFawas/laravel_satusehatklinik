<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pasien - Klinik Sehat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Custom scrollbar for better aesthetics */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #ABDCD6;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #9accc7;
        }
        /* Menggunakan custom color palette dari permintaan */
        .bg-custom-sidebar { background-color: #E9E6E6; }
        .bg-custom-button { background-color: #24306E; }
        .text-custom-button { color: #24306E; }
        .bg-custom-container { background-color: #ABDCD6; }
        .bg-custom-status-waiting { background-color: #facc15; color: #713f12; } /* Kuning */
        .bg-custom-status-called { background-color: #4ade80; color: #166534; } /* Hijau */
        .bg-custom-status-finished { background-color: #E9E6E6; color: #4b5563; } /* Abu-abu */
    </style>
</head>
<body class="bg-gray-100 font-sans" style="background-image: url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80'); background-size: cover; background-attachment: fixed;">

    <div class="flex flex-col lg:flex-row min-h-screen">
        <!-- Sidebar -->
        <aside class="w-full lg:w-64 bg-custom-sidebar text-gray-800 p-4 shadow-lg lg:fixed lg:h-screen">
            <div class="flex items-center justify-between lg:justify-center mb-10">
                <div class="flex items-center">
                    <i class="fas fa-clinic-medical text-4xl text-custom-button"></i>
                    <h1 class="text-2xl font-bold ml-2 text-custom-button">Klinik Sehat</h1>
                </div>
                <button id="mobile-menu-button" class="lg:hidden text-2xl">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <nav id="mobile-menu" class="hidden lg:block">
                <ul>
                    <li class="mb-4">
                        <a href="#" class="flex items-center p-3 rounded-lg bg-custom-button text-white shadow">
                            <i class="fas fa-tachometer-alt w-6"></i>
                            <span class="ml-3">Dashboard</span>
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-300 transition-colors">
                            <i class="fas fa-history w-6"></i>
                            <span class="ml-3">Riwayat Kunjungan</span>
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-300 transition-colors">
                            <i class="fas fa-user-md w-6"></i>
                            <span class="ml-3">Jadwal Dokter</span>
                        </a>
                    </li>
                    <li class="mb-4">
                        <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-300 transition-colors">
                            <i class="far fa-newspaper w-6"></i>
                            <span class="ml-3">Artikel</span>
                        </a>
                    </li>
                </ul>
                <div class="absolute bottom-4 left-4 right-4">
                    <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-300 transition-colors">
                        <i class="fas fa-user-circle w-6"></i>
                        <span class="ml-3">Profile Akun</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 p-4 sm:p-6 md:p-8">
            <!-- Header -->
            <header class="flex justify-between items-center mb-8 p-4 bg-white/80 backdrop-blur-sm rounded-xl shadow-md">
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-700">Selamat Datang, Adinda Rahmanda Putri</h2>
                <button class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors">
                    Keluar
                </button>
            </header>

            <!-- Dashboard Content -->
            <div id="dashboard-content" class="space-y-8">
                
                <!-- State: No Queue -->
                <div id="no-queue-state" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="md:col-span-1 p-6 rounded-2xl shadow-xl bg-white/90 backdrop-blur-sm flex flex-col items-center justify-center text-center">
                            <img src="https://storage.googleapis.com/gemini-prod-us-west1-assets/generated_images/6353381a-6d65-4df3-9e45-733d7b7e090a.png?Expires=1725170400&GoogleAccessId=gcs-assets-prod%40mythic-dream-399709.iam.gserviceaccount.com&Signature=Vp6F1q22%2F6L%2FFHhV9hU5x0pM6X4yL6GkP7pDkH6d3k7Yf7dF7Hw3g%2BE%2B9bL2g%2Bv7n8h6e7p8e7d7g%2Bv7n8h6e7p8e7d7g%2Bv7n8h6e7p8e7d7g%2Bv7n8h6e7p8e7d7g%2Bv7n8h6e7p8e7d7g%2Bv7n8h6e7p8e7d7g%3D%3D" alt="Antrean Online" class="w-32 h-32 mb-4">
                            <h3 class="text-xl font-bold text-custom-button mb-1">Antrean Online</h3>
                            <p class="text-gray-600 mb-4">Antrean Online, Lebih Efisien untuk Semua</p>
                            <button id="ambil-antrian-btn" class="bg-custom-button hover:bg-blue-900 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition-transform transform hover:scale-105">
                                Ambil Antrian
                            </button>
                        </div>
                        <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-8">
                            <div class="p-6 rounded-2xl shadow-xl bg-custom-container/90 backdrop-blur-sm flex flex-col justify-center items-center">
                                 <h3 class="text-lg font-bold text-custom-button mb-4">Nomor Antrean Berobat</h3>
                                 <p class="text-gray-700 text-center">Belum ada antrean dibuat.<br>Silahkan buat antrian baru.</p>
                            </div>
                            <div class="p-6 rounded-2xl shadow-xl bg-custom-container/90 backdrop-blur-sm flex flex-col justify-center items-center">
                                 <h3 class="text-lg font-bold text-custom-button mb-4">Nomor Antrean Apotek</h3>
                                 <p class="text-gray-700 text-center">Belum ada pemeriksaan terbaru.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- State: Queue Active -->
                <div id="queue-active-state" class="hidden">
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                         <!-- Kolom Kiri: Antrean Berobat -->
                         <div class="p-6 rounded-2xl shadow-xl bg-custom-container/90 backdrop-blur-sm flex flex-col justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-custom-button mb-4 text-center">Nomor Antrean Berobat</h3>
                                <div class="text-center mb-4">
                                    <p class="text-gray-600">Nomor Antrean Anda</p>
                                    <p id="queue-number" class="text-6xl font-extrabold text-custom-button">PU10</p>
                                    <span id="queue-status-badge" class="mt-2 inline-block px-4 py-1 text-sm font-semibold rounded-full">DIPANGGIL</span>
                                </div>
                                <hr class="my-4 border-gray-400">
                                <div class="text-center">
                                    <p class="text-gray-600">Status yang Sedang Dipanggil: <span id="current-serving" class="font-bold text-custom-button">PU09</span></p>
                                    <p class="text-gray-600">Estimasi Waktu Tunggu: <span id="wait-time" class="font-bold text-custom-button">5 Menit</span></p>
                                </div>
                            </div>
                            <button id="cancel-queue-btn" class="w-full mt-6 bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                                Batalkan Antrian
                            </button>
                         </div>
                         <!-- Kolom Kanan: Antrean Apotek -->
                         <div class="p-6 rounded-2xl shadow-xl bg-custom-container/90 backdrop-blur-sm flex flex-col justify-center items-center">
                             <h3 class="text-lg font-bold text-custom-button mb-4">Nomor Antrean Apotek</h3>
                             <p class="text-gray-700 text-center">Belum ada pemeriksaan terbaru.</p>
                         </div>
                     </div>
                </div>

                <!-- Artikel Kesehatan -->
                <div class="mt-8">
                    <h3 class="text-2xl font-bold mb-4 text-white">Artikel Kesehatan</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Artikel 1 -->
                        <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-lg overflow-hidden">
                            <img class="w-full h-48 object-cover" src="https://images.unsplash.com/photo-1579165466949-c9247c3d3de1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1974&q=80" alt="Artikel 1">
                            <div class="p-4">
                                <h4 class="font-bold text-lg mb-2">Banyak Kasus Keracunan, Epidemiolog Minta MBG Dihentikan Sementara</h4>
                                <p class="text-gray-600 text-sm">Berita ini menyoroti pentingnya keamanan pangan dan pengawasan...</p>
                            </div>
                        </div>
                        <!-- Artikel 2 -->
                        <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-lg overflow-hidden">
                            <img class="w-full h-48 object-cover" src="https://images.unsplash.com/photo-1528825871115-3581a5387919?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1915&q=80" alt="Artikel 2">
                            <div class="p-4">
                                <h4 class="font-bold text-lg mb-2">Manfaat Pepaya Ternyata Luar Biasa dengan Kandungan Bioaktif</h4>
                                <p class="text-gray-600 text-sm">Pepaya kaya akan vitamin, mineral, dan senyawa antioksidan...</p>
                            </div>
                        </div>
                        <!-- Artikel 3 -->
                        <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-lg overflow-hidden">
                            <img class="w-full h-48 object-cover" src="https://images.unsplash.com/photo-1618939307313-a21c2c5445aa?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" alt="Artikel 3">
                            <div class="p-4">
                                <h4 class="font-bold text-lg mb-2">Pencegahan Penyakit Demam Berdarah</h4>
                                <p class="text-gray-600 text-sm">Pentingnya menjaga kebersihan lingkungan untuk mencegah nyamuk...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- DATA SIMULASI ---
            // Anda bisa mengganti ini dengan data dari API
            const mockData = {
                hasQueue: false, // Ubah ke true untuk melihat state antrean aktif
                queueDetails: {
                    number: 'PU10',
                    status: 'menunggu', // 'menunggu', 'dipanggil', 'selesai'
                    servingNumber: 'PU09',
                    estimatedWaitMinutes: 5,
                    // Waktu panggil simulasi: 70 menit dari sekarang
                    // Ubah angka 70 menjadi 50 untuk menguji tombol batal yang nonaktif
                    estimatedCallTime: new Date(new Date().getTime() + 70 * 60000) 
                }
            };

            // --- ELEMEN UI ---
            const noQueueState = document.getElementById('no-queue-state');
            const queueActiveState = document.getElementById('queue-active-state');
            const ambilAntrianBtn = document.getElementById('ambil-antrian-btn');

            // Elemen di kartu antrean aktif
            const queueNumberEl = document.getElementById('queue-number');
            const queueStatusBadgeEl = document.getElementById('queue-status-badge');
            const currentServingEl = document.getElementById('current-serving');
            const waitTimeEl = document.getElementById('wait-time');
            const cancelQueueBtn = document.getElementById('cancel-queue-btn');

            // --- LOGIKA UTAMA ---
            function renderDashboard(data) {
                if (data.hasQueue) {
                    noQueueState.classList.add('hidden');
                    queueActiveState.classList.remove('hidden');
                    updateQueueCard(data.queueDetails);
                } else {
                    noQueueState.classList.remove('hidden');
                    queueActiveState.classList.add('hidden');
                }
            }

            function updateQueueCard(details) {
                queueNumberEl.textContent = details.number;
                currentServingEl.textContent = details.servingNumber;
                waitTimeEl.textContent = `${details.estimatedWaitMinutes} Menit`;

                // Update status dan warna badge
                queueStatusBadgeEl.classList.remove('bg-custom-status-waiting', 'bg-custom-status-called', 'bg-custom-status-finished');
                if (details.status === 'menunggu') {
                    queueStatusBadgeEl.textContent = 'MENUNGGU DIPANGGIL';
                    queueStatusBadgeEl.classList.add('bg-custom-status-waiting');
                } else if (details.status === 'dipanggil') {
                    queueStatusBadgeEl.textContent = 'DIPANGGIL';
                    queueStatusBadgeEl.classList.add('bg-custom-status-called');
                } else if (details.status === 'selesai') {
                    queueStatusBadgeEl.textContent = 'SELESAI';
                    queueStatusBadgeEl.classList.add('bg-custom-status-finished');
                }

                // Logika untuk menonaktifkan tombol batal
                const now = new Date();
                const timeDiffMinutes = (details.estimatedCallTime - now) / 60000;
                
                if (timeDiffMinutes < 60) {
                    cancelQueueBtn.disabled = true;
                    cancelQueueBtn.title = "Antrean tidak dapat dibatalkan kurang dari 60 menit sebelum panggilan.";
                } else {
                    cancelQueueBtn.disabled = false;
                    cancelQueueBtn.title = "";
                }
            }

            // --- EVENT LISTENERS ---
            ambilAntrianBtn.addEventListener('click', () => {
                // Simulasi mengambil antrian
                alert('Logika untuk mengambil antrian akan diimplementasikan di sini.');
                // Contoh perubahan state
                mockData.hasQueue = true;
                renderDashboard(mockData);
            });
            
            cancelQueueBtn.addEventListener('click', () => {
                if(confirm('Apakah Anda yakin ingin membatalkan antrean ini?')) {
                    alert('Logika untuk membatalkan antrean akan diimplementasikan di sini.');
                    // Contoh perubahan state
                    mockData.hasQueue = false;
                    renderDashboard(mockData);
                }
            });
            
            // --- Navigasi Mobile ---
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });


            // --- Render Awal ---
            renderDashboard(mockData);

            // --- (Opsional) Simulasi perubahan status antrean ---
            // Kode ini untuk demonstrasi perubahan warna status secara otomatis
            let statuses = ['menunggu', 'dipanggil', 'selesai'];
            let currentStatusIndex = 0;
            setInterval(() => {
                if (mockData.hasQueue) {
                    currentStatusIndex = (currentStatusIndex + 1) % statuses.length;
                    mockData.queueDetails.status = statuses[currentStatusIndex];
                    updateQueueCard(mockData.queueDetails);
                }
            }, 5000); // Ganti status setiap 5 detik

        });
    </script>
</body>
</html>
