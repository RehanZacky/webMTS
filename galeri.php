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
            <!-- Responsive logo: larger on mobile (h-20) and larger on sm+ (h-28) -->
            <div class="relative h-20 w-20 sm:h-28 sm:w-28">
            <img src="upload/logo/Logo_MTS.png" alt="Logo MTs Roudlotul Quran" class="logo-slide absolute inset-0 h-full w-full object-contain transition-opacity duration-1000 ease-in-out opacity-100">
            <img src="upload/logo/Logo_Ponpes.png" alt="Logo Ponpes Roudlotul Quran" class="logo-slide absolute inset-0 h-full w-full object-contain transition-opacity duration-1000 ease-in-out opacity-0">
            <img src="upload/logo/Logo_Yayasan.png" alt="Logo Yayasan Roudlotul Quran" class="logo-slide absolute inset-0 h-full w-full object-contain transition-opacity duration-1000 ease-in-out opacity-0">
    </div>
            <span class="text-xl font-bold text-white leading-tight">Yayasan Roudlotul Qur'an Az Zuhri <br> Pon.Pes & MTs Tahfidh <br> Roudlotul Qur'an </span>
        </a>

        <nav class="hidden md:flex items-center space-x-8">
            <a href="index.php" class="text-green-100 hover:text-white font-semibold">Beranda</a>
            <a href="profil.php" class="text-green-100 hover:text-white font-semibold">Profil</a>
            <a href="berita.php" class="text-green-100 hover:text-white font-semibold">Berita</a>
            <a href="prestasi.php" class="text-green-100 hover:text-white font-semibold">Prestasi</a>
            <a href="galeri.php" class="text-white font-bold">Galeri</a>
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

    <!-- Header scripts removed from here; initialization happens at the bottom of the page to avoid duplicate bindings -->

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


<?php include 'footer.php'; ?>

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

    // Improved script untuk toggle menu mobile (hamburger menu) + logo slider + footer rotator
    (function () {
        function initHeaderInteractions() {
            const btn = document.getElementById('menu-toggle');
            const menu = document.getElementById('mobile-menu');

            // Ensure menu has an initial inline display matching the 'hidden' class
            if (menu) {
                if (menu.classList.contains('hidden')) {
                    menu.style.display = 'none';
                    menu.setAttribute('aria-hidden', 'true');
                } else {
                    menu.style.display = '';
                    menu.setAttribute('aria-hidden', 'false');
                }
            }

            if (btn && menu) {
                // Accessibility attributes
                btn.setAttribute('aria-controls', 'mobile-menu');
                btn.setAttribute('aria-expanded', menu.classList.contains('hidden') ? 'false' : 'true');

                btn.addEventListener('click', function () {
                    const isHidden = menu.classList.toggle('hidden');
                    // Toggle inline display as a fallback if CSS utilities get overridden
                    if (isHidden) {
                        // was visible, now hidden
                        menu.style.display = 'none';
                        menu.setAttribute('aria-hidden', 'true');
                        btn.setAttribute('aria-expanded', 'false');
                    } else {
                        // was hidden, now visible
                        menu.style.display = 'block';
                        // Force a reflow to ensure the browser applies the change
                        void menu.offsetHeight;
                        menu.setAttribute('aria-hidden', 'false');
                        btn.setAttribute('aria-expanded', 'true');
                    }
                });
            }

            // Header logo slider
            const logos = document.querySelectorAll('.logo-slide');
            if (logos.length > 1) {
                let currentLogoIndex = 0;
                // ensure initial state
                logos.forEach((el, i) => {
                    el.classList.toggle('opacity-100', i === 0);
                    el.classList.toggle('opacity-0', i !== 0);
                });
                setInterval(() => {
                    logos[currentLogoIndex].classList.remove('opacity-100');
                    logos[currentLogoIndex].classList.add('opacity-0');
                    currentLogoIndex = (currentLogoIndex + 1) % logos.length;
                    logos[currentLogoIndex].classList.remove('opacity-0');
                    logos[currentLogoIndex].classList.add('opacity-100');
                }, 3000);
            }

            // Footer logo rotator
            const footerLogos = document.querySelectorAll('.logo-slide-footer');
            if (footerLogos.length > 1) {
                let currentFooterIndex = 0;
                footerLogos.forEach((el, i) => el.classList.toggle('opacity-100', i === 0));
                setInterval(() => {
                    footerLogos[currentFooterIndex].classList.remove('opacity-100');
                    footerLogos[currentFooterIndex].classList.add('opacity-0');
                    currentFooterIndex = (currentFooterIndex + 1) % footerLogos.length;
                    footerLogos[currentFooterIndex].classList.remove('opacity-0');
                    footerLogos[currentFooterIndex].classList.add('opacity-100');
                }, 3000);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initHeaderInteractions);
        } else {
            initHeaderInteractions();
        }
    })();
</script>

</body>
</html>