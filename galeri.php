<?php
// Koneksi ke database
include 'koneksi.php';

// Mengambil semua data dari tabel profil untuk digunakan di halaman ini
$profil_query = mysqli_query($koneksi, "SELECT jenis, isi FROM profil");
$profil_data = [];
while ($row = mysqli_fetch_assoc($profil_query)) {
    $profil_data[$row['jenis']] = $row['isi'];
}

// --- LOGIKA PAGINASI ---
$per_halaman = 6;
$halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_aktif = max(1, $halaman_aktif);

$hasil_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM galeri");
$data_total = mysqli_fetch_assoc($hasil_total);
$total_gambar = $data_total['total'];
$total_halaman = ceil($total_gambar / $per_halaman);

$offset = ($halaman_aktif - 1) * $per_halaman;
$query_gambar = "SELECT * FROM galeri ORDER BY tanggal_post DESC LIMIT $per_halaman OFFSET $offset";
$galeri_query = mysqli_query($koneksi, $query_gambar);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="upload/logo/Logo_MTS.png" type="image/png">
    <title>Galeri - Madrasah Tsanawiyah Roudlotul Quran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fancybox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"/>
    <style>
        /* Image modal optimizations */
        .image-modal-overlay {
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }

        html { scroll-behavior: smooth; }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeInDown { animation: fadeInDown 0.6s ease-out forwards; }
    </style>
</head>
<body class="bg-gray-50">

   <header class="bg-green-700 sticky top-0 z-50 shadow-lg">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <a href="index.php" class="flex items-center gap-3">
            <div class="relative h-20 w-20">
            <img src="upload/logo/Logo_MTS.png" alt="Logo MTs Roudlotul Quran" class="logo-slide absolute top-0 left-0 h-20 w-20 transition-opacity duration-1000 ease-in-out opacity-100">
            <img src="upload/logo/Logo_Ponpes.png" alt="Logo Ponpes Roudlotul Quran" class="logo-slide absolute top-0 left-0 h-20 w-20 transition-opacity duration-1000 ease-in-out opacity-0">
            <img src="upload/logo/Logo_Yayasan.png" alt="Logo Yayasan Roudlotul Quran" class="logo-slide absolute top-0 left-0 h-20 w-20 transition-opacity duration-1000 ease-in-out opacity-0">
    </div>
            <span class="text-xl font-bold text-white leading-tight">Yayasan Roudlotul Qur'an Az Zuhri <br> Pon.Pes & MTs Tahfidh <br> Roudlotul Qur'an </span>
        </a>

        <nav class="hidden md:flex items-center space-x-8">
            <a href="index.php" class="text-white font-bold">Beranda</a>
            <a href="profil.php" class="text-green-100 hover:text-white font-semibold">Profil</a>
            <a href="berita.php" class="text-green-100 hover:text-white font-semibold">Berita</a>
            <a href="prestasi.php" class="text-green-100 hover:text-white font-semibold">Prestasi</a>
            <a href="galeri.php" class="text-green-100 hover:text-white font-semibold">Galeri</a>
        </nav>

        <div class="md:hidden">
            <button id="menu-toggle" class="focus:outline-none">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </div>

    <div id="mobile-menu" class="hidden md:hidden bg-green-700 px-6 pb-4">
        <a href="index.php" class="block py-2 text-white font-semibold">Beranda</a>
        <a href="profil.php" class="block py-2 text-green-100 hover:text-white">Profil</a>
        <a href="berita.php" class="block py-2 text-green-100 hover:text-white">Berita</a>
        <a href="prestasi.php" class="block py-2 text-green-100 hover:text-white">Prestasi</a>
        <a href="galeri.php" class="block py-2 text-green-100 hover:text-white">Galeri</a>
    </div>
</header>

<script>
    // Mobile menu toggle
    document.getElementById("menu-toggle").addEventListener("click", function () {
        const menu = document.getElementById("mobile-menu");
        menu.classList.toggle("hidden");
    });

    // Logo slider functionality
    document.addEventListener('DOMContentLoaded', () => {
        const logos = document.querySelectorAll('.logo-slide');
        if (logos.length > 1) { // Hanya berjalan jika ada lebih dari satu logo
            let currentLogoIndex = 0;
            setInterval(() => {
                // Sembunyikan logo saat ini dengan efek fade-out
                logos[currentLogoIndex].classList.remove('opacity-100');
                logos[currentLogoIndex].classList.add('opacity-0');
                
                // Tentukan indeks logo berikutnya
                currentLogoIndex = (currentLogoIndex + 1) % logos.length;

                // Tampilkan logo berikutnya dengan efek fade-in
                logos[currentLogoIndex].classList.remove('opacity-0');
                logos[currentLogoIndex].classList.add('opacity-100');
            }, 3000); // Ganti logo setiap 3 detik (3000 milidetik)
        }
    });
</script>

<main class="py-24">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16 animate-fadeInDown">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                Galeri & <span class="text-green-600">Dokumentasi</span>
            </h1>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto leading-relaxed">
                Jelajahi momen-momen berharga dari berbagai kegiatan dan acara yang diselenggarakan di lingkungan Madrasah Tsanawiyah Roudlotul Qur'an.
            </p>
            <div class="w-24 h-1 bg-gradient-to-r from-green-400 to-green-600 rounded-full mx-auto mt-6"></div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
                <?php
                if ($galeri_query && mysqli_num_rows($galeri_query) > 0) :
                    while ($foto = mysqli_fetch_assoc($galeri_query)) :
                ?>
                <a href="upload/gambar_galeri/<?= htmlspecialchars($foto['file_path']) ?>" 
                   data-fancybox="gallery" 
                   data-caption="<?= htmlspecialchars($foto['deskripsi']) ?>" 
                   class="group block rounded-lg overflow-hidden shadow-sm border border-gray-200 hover:shadow-xl transition-all duration-300">
                     <div class="aspect-square w-full overflow-hidden bg-gray-100">
                        <img src="upload/gambar_galeri/<?= htmlspecialchars($foto['file_path']) ?>" 
                             alt="<?= htmlspecialchars($foto['deskripsi']) ?>" 
                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                     </div>
                     <?php if (!empty($foto['deskripsi'])): ?>
                     <?php endif; ?>
                </a>
                <?php
                    endwhile;
                else:
                    echo '<p class="col-span-full text-center text-gray-500 py-10">Belum ada foto di galeri.</p>';
                endif;
                ?>
            </div>
        </div>

        <?php if ($total_halaman > 1): ?>
        <div class="mt-16 flex justify-center items-center gap-4 sm:gap-6">
            <?php if ($halaman_aktif > 1): ?>
                <a href="?halaman=<?= $halaman_aktif - 1 ?>" class="flex items-center px-4 py-3 sm:px-6 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-300 shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    <span class="hidden sm:inline ml-2">Sebelumnya</span>
                </a>
            <?php else: ?>
                <button disabled class="flex items-center px-4 py-3 sm:px-6 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    <span class="hidden sm:inline ml-2">Sebelumnya</span>
                </button>
            <?php endif; ?>

            <div class="bg-white rounded-lg px-4 py-3 sm:px-6 shadow-md border border-gray-200">
                <span class="text-gray-600 font-semibold">Halaman <?= $halaman_aktif ?> dari <?= $total_halaman ?></span>
            </div>

            <?php if ($halaman_aktif < $total_halaman): ?>
                <a href="?halaman=<?= $halaman_aktif + 1 ?>" class="flex items-center px-4 py-3 sm:px-6 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-300 shadow-md hover:shadow-lg">
                    <span class="hidden sm:inline mr-2">Selanjutnya</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            <?php else: ?>
                <button disabled class="flex items-center px-4 py-3 sm:px-6 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed">
                    <span class="hidden sm:inline mr-2">Selanjutnya</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</main>

<footer class="bg-gradient-to-br from-green-800 via-green-700 to-green-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-full h-full" style="background-image: url('grain.svg');"></div>
    </div>
    
    <div class="container mx-auto px-6 py-12 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
            <div class="lg:col-span-2">
                <div class="flex items-center mb-6">
                    <img src="upload/logo/Logo_MTS.png" alt="Roudlotul Quran" class="logo-slide-footer h-16 w-16 mr-4 rounded-full border-2 border-white/20">
                    <img src="upload/logo/Logo_Ponpes.png" alt="Logo Ponpes" class="logo-slide-footer h-16 w-16 mr-4 rounded-full border-2 border-white/20">
                    <img src="upload/logo/Logo_Yayasan.png" alt="Logo Yayasan" class="logo-slide-footer h-16 w-16 mr-4 rounded-full border-2 border-white/20">
                    <div>
                        <h4 class="text-white text-xl font-bold">Yayasan Roudlotul Qur'an Az Zuhri </h4>
                        <h4 class="text-green-200 text-lg font-semibold"> Pon.Pes & MTs Tahfidh <br> Roudlotul Qur'an</h4>
                    </div>
                </div>

                <p class="text-green-100 leading-relaxed mb-6">
                    <?= htmlspecialchars($profil_data['tag_line'] ?? 'Madrasah Tsanawiyah Roudlotul Quran berkomitmen untuk mendidik generasi muda Islami yang berakhlak mulia, berilmu pengetahuan, dan berjiwa pemimpin.') ?>
                </p>
                
                <div class="flex space-x-4">
                    <a href="https://www.youtube.com/@MTSROUDLOTULQURAN" class="bg-white/10 hover:bg-white/20 p-3 rounded-full transition-all duration-300 hover:scale-110" aria-label="YouTube">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </a>
                    <a href="https://www.tiktok.com/@mtsroudlotulquran?_t=ZS-8yS8ESQSBPE&_r=1" class="bg-white/10 hover:bg-white/20 p-3 rounded-full transition-all duration-300 hover:scale-110" aria-label="TikTok">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                        </svg>
                    </a>
                    <a href="https://www.instagram.com/mtsroudlotulquran/" class="bg-white/10 hover:bg-white/20 p-3 rounded-full transition-all duration-300 hover:scale-110" aria-label="Instagram">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919C8.416 2.175 8.796 2.163 12 2.163M12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947C23.728 2.699 21.356.273 16.948.073 15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/>
                        </svg>
                    </a>
                </div>
            </div>
            
            <div>
                <h4 class="text-white text-lg font-bold mb-6 relative">
                    Menu Utama
                    <div class="absolute bottom-0 left-0 w-12 h-0.5 bg-green-300"></div>
                </h4>
                <ul class="space-y-3">
                    <li><a href="index.php" class="text-green-100 hover:text-white hover:pl-2 transition-all duration-300 flex items-center group">
                        <span class="w-1 h-1 bg-green-300 rounded-full mr-3 group-hover:w-2 transition-all duration-300"></span>
                        Beranda
                    </a></li>
                    <li><a href="profil.php" class="text-green-100 hover:text-white hover:pl-2 transition-all duration-300 flex items-center group">
                        <span class="w-1 h-1 bg-green-300 rounded-full mr-3 group-hover:w-2 transition-all duration-300"></span>
                        Profil
                    </a></li>
                    <li><a href="berita.php" class="text-green-100 hover:text-white hover:pl-2 transition-all duration-300 flex items-center group">
                        <span class="w-1 h-1 bg-green-300 rounded-full mr-3 group-hover:w-2 transition-all duration-300"></span>
                        Berita
                    </a></li>
                    <li><a href="prestasi.php" class="text-green-100 hover:text-white hover:pl-2 transition-all duration-300 flex items-center group">
                        <span class="w-1 h-1 bg-green-300 rounded-full mr-3 group-hover:w-2 transition-all duration-300"></span>
                        Prestasi
                    </a></li>
                    <li><a href="galeri.php" class="text-green-100 hover:text-white hover:pl-2 transition-all duration-300 flex items-center group">
                        <span class="w-1 h-1 bg-green-300 rounded-full mr-3 group-hover:w-2 transition-all duration-300"></span>
                        Galeri
                    </a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-white text-lg font-bold mb-6 relative">
                    Kontak Kami
                    <div class="absolute bottom-0 left-0 w-12 h-0.5 bg-green-300"></div>
                </h4>
                <ul class="space-y-4">
                    <li class="flex items-start group">
                        <div class="bg-white/10 p-2 rounded-lg mr-3 group-hover:bg-white/20 transition-colors duration-300">
                            <svg class="w-4 h-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-green-100 text-sm leading-relaxed">Dusun Tawangsari Rt.03 Rw.03<br>Desa Ngampelsari, Sidoarjo, Jawa Timur</p>
                        </div>
                    </li>
                    <li class="flex items-center group">
                        <div class="bg-white/10 p-2 rounded-lg mr-3 group-hover:bg-white/20 transition-colors duration-300">
                            <svg class="w-4 h-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <p class="text-green-100">082145964013</p>
                    </li>
                    <li class="flex items-center group">
                        <div class="bg-white/10 p-2 rounded-lg mr-3 group-hover:bg-white/20 transition-colors duration-300">
                            <svg class="w-4 h-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-green-100">mtsroudlotulquranngampelsari@gmail.com</p>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="pt-8 border-t border-white/10">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-4 md:mb-0">
                    <p class="text-green-100 text-sm">
                        © <?= date('Y') ?> Madrasah Tsanawiyah Roudlotul Qur'an. 
                        <span class="text-white font-semibold">Semua hak dilindungi undang-undang.</span><br>
                        <a href="https://hiimistis.carrd.co/" class="text-white font-semibold">Dibuat oleh Rehan, Ferdie, dan Nadhif.❤️</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Fancybox JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

<script>
  // Script untuk galeri foto Fancybox
  Fancybox.bind("[data-fancybox]", {
    // Optional configuration
    Thumbs: {
      autoStart: false,
    },
  });

  // Script untuk toggle menu mobile (hamburger menu)
  document.getElementById("menu-toggle").addEventListener("click", function () {
      const menu = document.getElementById("mobile-menu");
      menu.classList.toggle("hidden");
  });
</script>

</body>
</html>