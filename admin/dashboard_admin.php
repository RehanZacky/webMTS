<?php
include 'auth.php';
include '../koneksi.php';

// Ambil jumlah berita
$berita = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM berita");
$jumlah_berita = mysqli_fetch_assoc($berita)['total'];

// Ambil jumlah data statistik (siswa, guru, staff)
$statistik = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM info_statistik");
$jumlah_statistik = mysqli_fetch_assoc($statistik)['total'];

$username = $_SESSION['username'];

// Mock data untuk aktivitas terbaru
$aktivitas_terbaru = [
    ['aksi' => 'Berita baru ditambahkan', 'waktu' => '2 jam yang lalu', 'tipe' => 'berita'],
    ['aksi' => 'Statistik siswa diperbarui', 'waktu' => '5 jam yang lalu', 'tipe' => 'statistik'],
    ['aksi' => 'Profil sekolah diedit', 'waktu' => '1 hari yang lalu', 'tipe' => 'profil'],
    ['aksi' => 'Admin baru login', 'waktu' => '2 hari yang lalu', 'tipe' => 'auth']
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Modern Interface</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .stat-number {
            animation: countUp 2s ease-out;
        }
        
        @keyframes countUp {
            from { transform: scale(0.5); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        
        .notification-dot {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .progress-bar {
            animation: progressLoad 2s ease-out;
        }
        
        @keyframes progressLoad {
            from { width: 0%; }
            to { width: var(--progress-width); }
        }

        .nav-item {
            transition: all 0.3s ease;
        }

        .nav-item:hover {
            transform: translateY(-2px);
        }

        .dropdown {
            transition: all 0.3s ease;
        }

        .dropdown:hover .dropdown-content {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-content {
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Top Navigation -->
    <nav class="bg-gradient-to-r from-green-600 to-emerald-600 shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo and Brand -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-2xl font-bold text-white flex items-center">
                            <i class="fas fa-graduation-cap mr-2"></i>
                            Admin Panel
                        </h1>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="#" class="nav-item bg-green-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-chart-line mr-2"></i>Dashboard
                        </a>
                        <a href="berita_edit.php" class="nav-item text-green-100 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-newspaper mr-2"></i>Kelola Berita
                        </a>
                        <a href="statistik_edit.php" class="nav-item text-green-100 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-chart-bar mr-2"></i>Statistik
                        </a>
                        <a href="profil_edit.php" class="nav-item text-green-100 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-school mr-2"></i>Profil Sekolah
                        </a>
                        <a href="prestasi_edit.php" class="nav-item text-green-100 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-trophy mr-2"></i>Prestasi
                        </a>
                        <a href="#" class="nav-item text-green-100 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-images mr-2"></i>Galeri
                        </a>
                    </div>
                </div>

                <!-- Right side items -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div class="relative">
                        <button class="relative text-green-100 hover:text-white p-2 rounded-full hover:bg-green-700 transition-colors">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full notification-dot"></span>
                        </button>
                    </div>

                    <!-- Time -->
                    <div class="text-sm text-green-100">
                        <span id="currentTime"></span>
                    </div>

                    <!-- User Dropdown -->
                    <div class="relative dropdown">
                        <button class="flex items-center text-green-100 hover:text-white p-2 rounded-md hover:bg-green-700 transition-colors">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-2">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <span class="text-sm font-medium"><?= $username ?></span>
                            <i class="fas fa-chevron-down ml-2 text-xs"></i>
                        </button>
                        <div class="dropdown-content absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <div class="border-t border-gray-100"></div>
                            <a href="../logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobileMenuBtn" class="text-green-100 hover:text-white p-2">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobileMenu" class="md:hidden hidden bg-green-700">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="#" class="bg-green-800 text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-chart-line mr-2"></i>Dashboard
                </a>
                <a href="berita_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-newspaper mr-2"></i>Kelola Berita
                </a>
                <a href="statistik_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-chart-bar mr-2"></i>Statistik
                </a>
                <a href="profil_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-school mr-2"></i>Profil Sekolah
                </a>
                <a href="prestasi_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-trophy mr-2"></i>Prestasi
                </a>
                <a href="#" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-images mr-2"></i>Galeri
                </a>
                <div class="border-t border-green-600 pt-4">
                    <a href="../logout.php" class="text-red-300 hover:bg-red-600 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold mb-2">Selamat Datang, <?= $username ?>!</h1>
                        <p class="text-green-100">Kelola website sekolah Anda dengan mudah melalui dashboard ini</p>
                    </div>
                    <div class="hidden md:block">
                        <i class="fas fa-chart-line text-6xl text-green-200 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Content Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Recent News -->
            <div class="bg-white rounded-xl shadow-lg p-6 animate-fade-in" style="animation-delay: 0.4s;">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                   <i class="fas fa-newspaper text-green-500 mr-2"></i>
                    Berita Terbaru
                </h3>
                <div class="overflow-x-auto whitespace-nowrap space-x-4 flex pb-4">
                    <?php
                    $berita_query = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_post DESC LIMIT 5");                            
                    if (mysqli_num_rows($berita_query) > 0): ?>
                        <?php while ($b = mysqli_fetch_assoc($berita_query)) : ?>
                            <?php if (!empty($b['gambar_utama'])): ?>
                                <div class="inline-block w-80 flex-shrink-0 bg-gray-50 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                                    <a href="berita_detail.php?id=<?= $b['id'] ?>">
                                        <img src="upload/<?= $b['gambar_utama'] ?>" class="h-48 w-full object-cover rounded-t-lg" alt="Gambar Berita">
                                    </a>
                                    <div class="p-4">
                                        <p class="text-sm text-gray-500 mb-1 flex items-center">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            <?= date('d M Y', strtotime($b['tanggal_post'])) ?>
                                        </p>
                                        <h4 class="font-semibold text-gray-800 mb-2 line-clamp-2"><?= $b['judul'] ?></h4>
                                        <p class="text-sm text-gray-600 line-clamp-2"><?= substr(strip_tags($b['isi']), 0, 100) ?>...</p>
                                        <a href="berita_detail.php?id=<?= $b['id'] ?>" class="text-sm text-green-600 hover:underline mt-2 inline-block">Baca Selengkapnya</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <i class="fas fa-newspaper text-gray-300 text-4xl mb-4"></i>
                            <p class="text-gray-500">Belum ada berita yang dipublikasikan</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="mt-6">
                    <div class="flex space-x-3">
                        <a href="berita_edit.php" class="flex items-center justify-center flex-1 px-4 py-2 text-sm font-medium text-green-600 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Berita Baru
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white rounded-xl shadow-lg p-6 animate-fade-in" style="animation-delay: 0.5s;">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-clock text-green-500 mr-2"></i>
                    Aktivitas Terbaru
                </h3>
                <div class="space-y-4">
                    <?php foreach($aktivitas_terbaru as $index => $aktivitas): ?>
                    <div class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0">
                            <?php 
                            $iconClass = '';
                            $colorClass = '';
                            switch($aktivitas['tipe']) {
                                case 'berita':
                                    $iconClass = 'fas fa-newspaper';
                                   $colorClass = 'text-green-500';
                                    break;
                                case 'statistik':
                                    $iconClass = 'fas fa-chart-bar';
                                   $colorClass = 'text-emerald-500';
                                    break;
                                case 'profil':
                                    $iconClass = 'fas fa-school';
                                   $colorClass = 'text-teal-500';
                                    break;
                                default:
                                    $iconClass = 'fas fa-user';
                                    $colorClass = 'text-orange-500';
                            }
                            ?>
                            <i class="<?= $iconClass ?> <?= $colorClass ?>"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800"><?= $aktivitas['aksi'] ?></p>
                            <p class="text-xs text-gray-500"><?= $aktivitas['waktu'] ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6 animate-fade-in" style="animation-delay: 0.6s;">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
               <i class="fas fa-bolt text-green-500 mr-2"></i>
                Aksi Cepat
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
               <a href="berita_edit.php" class="flex flex-col items-center space-y-3 p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors group">
                   <div class="p-3 bg-green-500 rounded-full group-hover:scale-110 transition-transform">
                        <i class="fas fa-plus text-white text-xl"></i>
                    </div>
                    <div class="text-center">
                       <p class="font-medium text-green-700">Tambah Berita</p>
                       <p class="text-xs text-green-600">Buat artikel baru</p>
                    </div>
                </a>
                
               <a href="statistik_edit.php" class="flex flex-col items-center space-y-3 p-4 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors group">
                   <div class="p-3 bg-emerald-500 rounded-full group-hover:scale-110 transition-transform">
                        <i class="fas fa-chart-bar text-white text-xl"></i>
                    </div>
                    <div class="text-center">
                       <p class="font-medium text-emerald-700">Update Statistik</p>
                       <p class="text-xs text-emerald-600">Perbarui data</p>
                    </div>
                </a>
                
               <a href="profil_edit.php" class="flex flex-col items-center space-y-3 p-4 bg-teal-50 rounded-lg hover:bg-teal-100 transition-colors group">
                   <div class="p-3 bg-teal-500 rounded-full group-hover:scale-110 transition-transform">
                        <i class="fas fa-school text-white text-xl"></i>
                    </div>
                    <div class="text-center">
                       <p class="font-medium text-teal-700">Edit Profil</p>
                       <p class="text-xs text-teal-600">Ubah info sekolah</p>
                    </div>
                </a>

               <a href="prestasi_edit.php" class="flex flex-col items-center space-y-3 p-4 bg-lime-50 rounded-lg hover:bg-lime-100 transition-colors group">
                   <div class="p-3 bg-lime-500 rounded-full group-hover:scale-110 transition-transform">
                        <i class="fas fa-trophy text-white text-xl"></i>
                    </div>
                    <div class="text-center">
                       <p class="font-medium text-lime-700">Tambah Prestasi</p>
                       <p class="text-xs text-lime-600">Input prestasi</p>
                    </div>
                </a>

               <a href="#" class="flex flex-col items-center space-y-3 p-4 bg-cyan-50 rounded-lg hover:bg-cyan-100 transition-colors group">
                   <div class="p-3 bg-cyan-500 rounded-full group-hover:scale-110 transition-transform">
                        <i class="fas fa-images text-white text-xl"></i>
                    </div>
                    <div class="text-center">
                       <p class="font-medium text-cyan-700">Kelola Galeri</p>
                       <p class="text-xs text-cyan-600">Upload foto</p>
                    </div>
                </a>
                
               <a href="#" class="flex flex-col items-center space-y-3 p-4 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors group">
                   <div class="p-3 bg-slate-500 rounded-full group-hover:scale-110 transition-transform">
                        <i class="fas fa-cog text-white text-xl"></i>
                    </div>
                    <div class="text-center">
                       <p class="font-medium text-slate-700">Pengaturan</p>
                       <p class="text-xs text-slate-600">Konfigurasi sistem</p>
                    </div>
                </a>
            </div>
        </div>
    </main>

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Current time display
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            document.getElementById('currentTime').textContent = timeString;
        }

        setInterval(updateTime, 1000);
        updateTime();

        // Animate numbers on load
        document.addEventListener('DOMContentLoaded', function() {
            const numbers = document.querySelectorAll('.stat-number');
            numbers.forEach(number => {
                const finalNumber = parseInt(number.textContent);
                let currentNumber = 0;
                const increment = finalNumber / 50;
                
                const timer = setInterval(() => {
                    currentNumber += increment;
                    if (currentNumber >= finalNumber) {
                        number.textContent = finalNumber;
                        clearInterval(timer);
                    } else {
                        number.textContent = Math.floor(currentNumber);
                    }
                }, 40);
            });
        });

        // Add click effects to cards
        document.querySelectorAll('.card-hover').forEach(card => {
            card.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'translateY(-5px)';
                }, 150);
            });
        });

        // Add CSS for line clamp
        const style = document.createElement('style');
        style.textContent = `
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>