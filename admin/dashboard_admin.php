<?php
include 'auth.php';
include '../koneksi.php';
// Simulasi data untuk demo (ganti dengan query database asli)
$jumlah_berita = 45;
$jumlah_statistik = 3;
$username = "Admin User";
// Mock data untuk grafik dan statistik
$berita_terbaru = [
    ['judul' => 'Pelaksanaan Ujian Tengah Semester Ganjil 2024', 'tanggal' => '2024-01-15', 'views' => 245],
    ['judul' => 'Penerimaan Siswa Baru Tahun Ajaran 2024/2025', 'tanggal' => '2024-01-12', 'views' => 189],
    ['judul' => 'Kegiatan Ekstrakurikuler Semester Baru', 'tanggal' => '2024-01-10', 'views' => 156],
    ['judul' => 'Pengumuman Libur Semester Ganjil', 'tanggal' => '2024-01-08', 'views' => 298],
    ['judul' => 'Workshop Guru: Teknologi dalam Pembelajaran', 'tanggal' => '2024-01-05', 'views' => 87]
];
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
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        
        .sidebar {
            transition: transform 0.3s ease;
        }
        
        .sidebar-closed {
            transform: translateX(-100%);
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
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg sidebar lg:translate-x-0">
        <div class="flex items-center justify-between h-16 px-6 bg-gradient-to-r from-blue-600 to-purple-600">
            <h1 class="text-xl font-bold text-white">Admin Panel</h1>
            <button id="closeSidebar" class="lg:hidden text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <nav class="mt-8">
            <div class="px-6 py-3">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-700">Halo,</p>
                        <p class="text-sm text-gray-500"><?= $username ?></p>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 space-y-2">
                <a href="#" class="flex items-center px-6 py-3 text-gray-700 bg-blue-50 border-r-4 border-blue-500">
                    <i class="fas fa-chart-line mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="berita_edit.php" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                    <i class="fas fa-newspaper mr-3"></i>
                    <span>Kelola Berita</span>
                </a>
                <a href="statistik_edit.php" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                    <i class="fas fa-chart-bar mr-3"></i>
                    <span>Kelola Statistik</span>
                </a>
                <a href="profil_edit.php" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                    <i class="fas fa-school mr-3"></i>
                    <span>Profil Sekolah</span>
                </a>
                <a href="#" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                    <i class="fas fa-cog mr-3"></i>
                    <span>Tambah Prestasi</span>
                </a>
                    <a href="#" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                    <i class="fas fa-cog mr-3"></i>
                    <span>Tambah Gambar Galeri</span>
                </a>
            </div>
            
            <div class="absolute bottom-0 w-full p-6">
                <a href="../logout.php" class="flex items-center justify-center w-full px-4 py-2 text-white bg-red-500 rounded-lg hover:bg-red-600 transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="lg:ml-64">
        <!-- Top Bar -->
        <header class="bg-gradient-to-r from-blue-600 to-purple-600 shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between h-16 px-6">
                <div class="flex items-center">
                    <button id="openSidebar" class="lg:hidden text-gray-500 hover:text-gray-700">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2 class="ml-4 text-xl font-semibold text-white">Dashboard Overview</h2>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button class="relative text-gray-500 hover:text-gray-700">
                            <i class="fas fa-bell text-xl"></i>
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full notification-dot"></span>
                        </button>
                    </div>
                    <div class="text-sm text-white">
                        <span id="currentTime"></span>
                    </div>
                </div>
            </div>
        </header>
            <!-- Charts and Activities -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Recent News -->
                <div class="bg-white rounded-xl shadow-lg p-6 animate-fade-in" style="animation-delay: 0.4s;">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-newspaper text-blue-500 mr-2"></i>
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
                                        $colorClass = 'text-blue-500';
                                        break;
                                    case 'statistik':
                                        $iconClass = 'fas fa-chart-bar';
                                        $colorClass = 'text-green-500';
                                        break;
                                    case 'profil':
                                        $iconClass = 'fas fa-school';
                                        $colorClass = 'text-purple-500';
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
                    <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                    Aksi Cepat
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="berita_edit.php" class="flex items-center space-x-3 p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors group">
                        <div class="p-2 bg-blue-500 rounded-lg group-hover:scale-110 transition-transform">
                            <i class="fas fa-plus text-white"></i>
                        </div>
                        <div>
                            <p class="font-medium text-blue-700">Tambah Berita</p>
                            <p class="text-sm text-blue-600">Buat artikel baru</p>
                        </div>
                    </a>
                    
                    <a href="statistik_edit.php" class="flex items-center space-x-3 p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors group">
                        <div class="p-2 bg-green-500 rounded-lg group-hover:scale-110 transition-transform">
                            <i class="fas fa-edit text-white"></i>
                        </div>
                        <div>
                            <p class="font-medium text-green-700">Update Statistik</p>
                            <p class="text-sm text-green-600">Perbarui data</p>
                        </div>
                    </a>
                    
                    <a href="profil_edit.php" class="flex items-center space-x-3 p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors group">
                        <div class="p-2 bg-purple-500 rounded-lg group-hover:scale-110 transition-transform">
                            <i class="fas fa-school text-white"></i>
                        </div>
                        <div>
                            <p class="font-medium text-purple-700">Edit Profil</p>
                            <p class="text-sm text-purple-600">Ubah info sekolah</p>
                        </div>
                    </a>
                    
                    <a href="#" class="flex items-center space-x-3 p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors group">
                        <div class="p-2 bg-orange-500 rounded-lg group-hover:scale-110 transition-transform">
                            <i class="fas fa-cog text-white"></i>
                        </div>
                        <div>
                            <p class="font-medium text-orange-700">Pengaturan</p>
                            <p class="text-sm text-orange-600">Konfigurasi sistem</p>
                        </div>
                    </a>
                </div>
            </div>
        </main>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

    <script>
        // Sidebar toggle functionality
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const openSidebar = document.getElementById('openSidebar');
        const closeSidebar = document.getElementById('closeSidebar');

        openSidebar.addEventListener('click', () => {
            sidebar.classList.remove('sidebar-closed');
            sidebarOverlay.classList.remove('hidden');
        });

        closeSidebar.addEventListener('click', () => {
            sidebar.classList.add('sidebar-closed');
            sidebarOverlay.classList.add('hidden');
        });

        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.add('sidebar-closed');
            sidebarOverlay.classList.add('hidden');
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

        // Add some interactive animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate numbers on load
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