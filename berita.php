<?php
include 'koneksi.php';

// Mengambil semua data dari tabel profil untuk digunakan di halaman ini
$profil_query = mysqli_query($koneksi, "SELECT jenis, isi FROM profil");
$profil_data = [];
while ($row = mysqli_fetch_assoc($profil_query)) {
    $profil_data[$row['jenis']] = $row['isi'];
}

// --- LOGIKA PAGINASI UNTUK BERITA ---
// [DIUBAH] Menampilkan 6 item per halaman
$per_halaman = 6;
$halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_aktif = max(1, $halaman_aktif);

$query_total = "SELECT COUNT(*) as total FROM berita";
$hasil_total = mysqli_query($koneksi, $query_total);
$data_total = mysqli_fetch_assoc($hasil_total);
$total_berita = $data_total['total'];

$total_halaman = ceil($total_berita / $per_halaman);
$offset = ($halaman_aktif - 1) * $per_halaman;

$query_berita = "SELECT * FROM berita ORDER BY tanggal_post DESC LIMIT $per_halaman OFFSET $offset";
$berita_result = mysqli_query($koneksi, $query_berita);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita - Madrasah Tsanawiyah Roudlotul Qur'an</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="upload/logo/Logo_MTS.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeInDown { animation: fadeInDown 0.6s ease-out forwards; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">

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
            <a href="berita.php" class="text-white font-bold">Berita</a>
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
        <a href="index.php" class="text-green-100 hover:text-white font-semibold">Beranda</a>
        <a href="profil.php" class="block py-2 text-green-100 hover:text-white">Profil</a>
        <a href="berita.php" class="text-white font-bold">Berita</a>
        <a href="prestasi.php" class="block py-2 text-green-100 hover:text-white">Prestasi</a>
        <a href="galeri.php" class="block py-2 text-green-100 hover:text-white">Galeri</a>
    </div>
</header>

<script>
    // Initialize mobile menu toggle and logo slider — run immediately or on DOMContentLoaded
    (function () {
        function initHeaderInteractions() {
            const btn = document.getElementById('menu-toggle');
            const menu = document.getElementById('mobile-menu');
            if (btn && menu) {
                btn.addEventListener('click', function () {
                    menu.classList.toggle('hidden');
                });
            }

            const logos = document.querySelectorAll('.logo-slide');
            if (logos.length > 1) {
                let currentLogoIndex = 0;
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

<main class="py-24">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12 sm:mb-16 animate-fadeInDown">
            <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-gray-800 mb-3 sm:mb-4">
                Artikel & <span class="text-green-600">Berita</span>
            </h1>
            <p class="text-sm sm:text-base md:text-lg text-gray-600 max-w-3xl mx-auto leading-relaxed px-2">
                Ikuti informasi terkini seputar kegiatan, pengumuman, dan pencapaian membanggakan dari para santri di Madrasah Tsanawiyah Roudlotul Qur'an.
            </p>
            <div class="w-24 h-1 bg-gradient-to-r from-green-400 to-green-600 rounded-full mx-auto mt-6"></div>
        </div>

        <div class="bg-white rounded-lg sm:rounded-2xl shadow-lg p-4 sm:p-6 md:p-8 lg:p-12">
             <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
                <?php
                if ($berita_result && mysqli_num_rows($berita_result) > 0) {
                    while ($b = mysqli_fetch_assoc($berita_result)) {
                        ?>
                        <div class="bg-gray-50 rounded-lg overflow-hidden flex flex-col transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group">
                            <a href="berita_detail.php?id=<?= $b['id'] ?>">
                                <div class="aspect-video w-full overflow-hidden bg-gray-100">
                                    <img src="upload/gambar_berita/<?= htmlspecialchars($b['gambar_utama']) ?>" class="w-full h-full object-contain transition-transform duration-300 group-hover:scale-105" alt="Gambar: <?= htmlspecialchars($b['judul']) ?>">
                                </div>
                            </a>
                            <div class="p-3 sm:p-4 md:p-5 flex flex-col flex-grow">
                                <p class="text-xs text-gray-500 mb-2"><?= date('d M Y', strtotime($b['tanggal_post'])) ?></p>
                                <h3 class="text-sm sm:text-base md:text-base lg:text-lg font-bold text-gray-800 mb-2 flex-grow line-clamp-3">
                                    <a href="berita_detail.php?id=<?= $b['id'] ?>" class="hover:text-green-600">
                                        <?= htmlspecialchars($b['judul']) ?>
                                    </a>
                                </h3>
                                <a href="berita_detail.php?id=<?= $b['id'] ?>" class="text-sm font-semibold text-green-600 hover:underline mt-2 self-start">Baca Selengkapnya →</a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p class='col-span-full text-center text-gray-500'>Belum ada berita yang ditambahkan.</p>";
                }
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