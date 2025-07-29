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

// Ambil data statistik sekolah dari database
$statistik_query = mysqli_query($koneksi, "SELECT * FROM info_statistik WHERE id IN (1,2,3,4,5,6) ORDER BY id ASC");
$statistik_data = [];
while ($row = mysqli_fetch_assoc($statistik_query)) {
    $statistik_data[$row['id']] = $row;
}
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

        .statistik-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .statistik-card:hover {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-color: #10b981;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px -5px rgba(16, 185, 129, 0.1);
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #10b981, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-icon-bg {
            background: linear-gradient(135deg, #10b981, #059669);
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
                  <a href="dashboard_admin.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-chart-line mr-2"></i>Dashboard
                  </a>
                    <a href="statistik_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-chart-bar mr-2"></i>Statistik
                  </a>
                <a href="profil_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-school mr-2"></i>Profil
                </a>
                <a href="pegawai_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-users mr-2"></i>Guru/Staff
                </a>
                <a href="berita_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-newspaper mr-2"></i>Berita
                </a>
                <a href="prestasi_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-trophy mr-2"></i>Prestasi
                </a>
                <a href="galeri_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-images mr-2"></i>Galeri
                </a>
                        <a href="../logout.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                    </div>
                </div>

                <!-- Right side items -->
                <div class="flex items-center justify-end space-x-4">
                    <!-- User Info -->
                    <div class="flex items-center text-green-100">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-2">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                        <span class="text-sm font-medium"><?= $username ?></span>
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
                <a href="dashboard_admin.php" class="bg-green-800 text-white block px-3 py-2 rounded-md text-base font-medium"">
                    <i class="fas fa-chart-line mr-2"></i>Dashboard
                </a>
                <a href="statistik_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-chart-bar mr-2"></i>Statistik
                </a>
                <a href="profil_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-school mr-2"></i>Profil Sekolah
                </a>
                <a href="pegawai_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-users mr-2"></i>Guru/Staff
                </a>
                <a href="berita_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-newspaper mr-2"></i>Berita
                </a>
                <a href="prestasi_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-trophy mr-2"></i>Prestasi
                </a>
                <a href="galeri_edit.php" class="text-green-100 hover:bg-green-800 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
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
                        <!-- Berita Terbaru -->
            <div class="bg-white rounded-xl shadow-lg p-6 animate-fade-in" style="animation-delay: 0.4s;">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                   <i class="fas fa-newspaper text-green-500 mr-2"></i>
                    Berita Terbaru
                </h3>
                
                <div class="overflow-x-auto">
                    <div class="flex space-x-4 pb-4">
                        <?php
                        $berita_query = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_post DESC LIMIT 5");                            
                        if (mysqli_num_rows($berita_query) > 0): ?>
                            <?php while ($berita = mysqli_fetch_assoc($berita_query)) : ?>
                                <div class="flex-shrink-0 w-64 bg-gray-50 rounded-lg border border-gray-200 hover:shadow-md transition-all duration-300 hover:border-green-300 group">
                                    <!-- Gambar Preview (Persegi) -->
                                    <?php if (!empty($berita['gambar_utama'])): ?>
                                        <div class="w-full h-40">
                                            <img src="../upload/gambar_berita/<?= htmlspecialchars($berita['gambar_utama']) ?>" 
                                                 alt="<?= htmlspecialchars($berita['judul']) ?>"
                                                 class="w-full h-full object-cover rounded-t-lg">
                                        </div>
                                    <?php else: ?>
                                        <div class="w-full h-40 bg-gradient-to-br from-green-400 to-green-600 rounded-t-lg flex items-center justify-center">
                                            <i class="fas fa-newspaper text-white text-3xl"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Konten Berita -->
                                    <div class="p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="text-xs text-gray-500 flex items-center">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                <?= date('d M Y', strtotime($berita['tanggal_post'])) ?>
                                            </p>
                                        </div>
                                        
                                        <h4 class="font-semibold text-gray-800 mb-2 text-sm leading-tight line-clamp-2 group-hover:text-green-700 transition-colors">
                                            <?= htmlspecialchars($berita['judul']) ?>
                                        </h4>
                                        
                                        <p class="text-xs text-gray-600 leading-relaxed line-clamp-3 mb-3">
                                            <?= substr(strip_tags($berita['isi']), 0, 100) ?>...
                                        </p>
                                        
                                        <div class="flex items-center justify-between">
                                            <a href="../berita_detail.php?id=<?= $berita['id'] ?>" 
                                               class="text-xs text-green-600 hover:text-green-700 font-medium hover:underline transition-colors">
                                                Baca Selengkapnya
                                            </a>
                                            <a href="berita_edit.php?id=<?= $berita['id'] ?>" 
                                               class="text-xs text-blue-600 hover:text-blue-700 transition-colors p-1 rounded hover:bg-blue-50" 
                                               title="Edit Berita">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="w-full text-center py-12">
                                <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-newspaper text-gray-400 text-2xl"></i>
                                </div>
                                <h4 class="text-gray-600 font-medium mb-2">Belum ada berita</h4>
                                <p class="text-gray-500 text-sm">Mulai publikasikan berita pertama Anda</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Footer Actions -->
                <div class="mt-6 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-list-ul mr-2"></i>
                            Total: <?= mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM berita")) ?> berita
                        </div>
                        <a href="../berita.php" class="text-sm text-green-600 hover:text-green-700 font-medium">
                            Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    
                    <div class="grid gap-3">
                        <a href="berita_edit.php" class="flex items-center justify-center px-4 py-2 text-sm font-medium text-green-600 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Berita
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistik Sekolah -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-chart-bar text-green-500 mr-2"></i>
                    Statistik Sekolah
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <?php 
                    $statistik_labels = [
                        1 => ['label' => 'Siswa Aktif', 'icon' => 'fas fa-user-graduate', 'color' => 'text-blue-500', 'suffix' => ' Siswa'],
                        2 => ['label' => 'Akreditasi', 'icon' => 'fas fa-medal', 'color' => 'text-yellow-500', 'suffix' => ''],
                        3 => ['label' => 'Jumlah Kelas', 'icon' => 'fas fa-door-open', 'color' => 'text-purple-500', 'suffix' => ' Kelas'],
                        4 => ['label' => 'Guru & Staff', 'icon' => 'fas fa-users', 'color' => 'text-green-500', 'suffix' => ' Orang'],
                        5 => ['label' => 'Alumni', 'icon' => 'fas fa-user-tie', 'color' => 'text-indigo-500', 'suffix' => ' Alumni'],
                        6 => ['label' => 'Mata Pelajaran', 'icon' => 'fas fa-book', 'color' => 'text-red-500', 'suffix' => ' Mapel']
                    ];
                    
                    foreach ($statistik_labels as $id => $info): 
                        $data = isset($statistik_data[$id]) ? $statistik_data[$id] : null;
                        $nilai = $data ? $data['nilai'] : '0';
                        $label = $data ? $data['label'] : $info['label'];
                    ?>
                    <div class="bg-gray-50 rounded-lg p-8 hover:shadow-md transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="stat-icon-bg p-2 rounded-full">
                                    <i class="<?= $info['icon'] ?> text-white text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-700 text-sm"><?= $label ?></h4>
                                    <div class="flex items-baseline space-x-1">
                                        <span class="stat-value stat-number"><?= $nilai ?></span>
                                        <?php if (!empty($info['suffix']) && is_numeric($nilai)): ?>
                                            <span class="text-xs text-gray-500"><?= $info['suffix'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                        
                
                <div class="mt-4">
                    <div class="flex space-x-3">
                        <a href="statistik_edit.php" class="flex items-center justify-center flex-1 px-4 py-2 text-sm font-medium text-green-600 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <i class="fas fa-edit mr-2"></i>
                            Update Statistik
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6" style="animation-delay: 0.6s;">
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

               <a href="galeri_edit.php" class="flex flex-col items-center space-y-3 p-4 bg-cyan-50 rounded-lg hover:bg-cyan-100 transition-colors group">
                   <div class="p-3 bg-cyan-500 rounded-full group-hover:scale-110 transition-transform">
                        <i class="fas fa-images text-white text-xl"></i>
                    </div>
                    <div class="text-center">
                       <p class="font-medium text-cyan-700">Kelola Galeri</p>
                       <p class="text-xs text-cyan-600">Upload foto</p>
                    </div>
                </a>
                
               <a href="pegawai_edit.php" class="flex flex-col items-center space-y-3 p-4 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors group">
                   <div class="p-3 bg-slate-500 rounded-full group-hover:scale-110 transition-transform">
                        <i class="fas fa-user text-white text-xl"></i>
                    </div>
                    <div class="text-center">
                       <p class="font-medium text-slate-700">Guru & Staff</p>
                       <p class="text-xs text-slate-600">Tambah Guru dan Staff</p>
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