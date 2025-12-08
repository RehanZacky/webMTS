<?php
// Koneksi ke database
include 'koneksi.php';

// Mengambil semua data dari tabel profil untuk digunakan di halaman ini
$profil_query = mysqli_query($koneksi, "SELECT jenis, isi FROM profil");
$profil_data = [];
while ($row = mysqli_fetch_assoc($profil_query)) {
    $profil_data[$row['jenis']] = $row['isi'];
}


// Mengambil data dari tabel `pegawai` dan diurutkan berdasarkan kolom `urutan`
$query_pegawai = "SELECT * FROM pegawai ORDER BY urutan ASC";
$pegawai_result = mysqli_query($koneksi, $query_pegawai);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pegawai - Madrasah Tsanawiyah Roudlotul Qur'an</title>
    <link rel="icon" href="upload/logo/Logo_MTS.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .image-modal-overlay {
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }

        html {
            scroll-behavior: smooth;
        }
        /* CSS untuk animasi (opsional, bisa dihapus jika tidak ingin animasi sama sekali) */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
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
            <a href="profil.php" class="text-white font-bold">Profil</a>
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

    <main class="pt-24 pb-16 md:pt-32 md:pb-24">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 animate-fadeInDown">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                    Profil Guru & <span class="text-green-600">Staff</span>
                </h1>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Tenaga pendidik dan staf yang berdedikasi di Madrasah Tsanawiyah Roudlotul Qur'an, siap membimbing santri menuju kesuksesan dunia dan akhirat.
                </p>
                <div class="w-24 h-1 bg-gradient-to-r from-green-400 to-green-600 rounded-full mx-auto mt-6"></div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12 animate-fadeInUp">
                <?php if ($pegawai_result && mysqli_num_rows($pegawai_result) > 0) : ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php while ($pegawai = mysqli_fetch_assoc($pegawai_result)) : ?>
                            <div class="bg-gray-50 rounded-xl p-8 text-center flex flex-col items-center transition-all duration-300 hover:shadow-xl hover:bg-white hover:-translate-y-1">
                                <img src="upload/gambar_pegawai/<?= htmlspecialchars($pegawai['foto']) ?>" alt="Foto <?= htmlspecialchars($pegawai['nama']) ?>" class="w-28 h-28 rounded-full object-cover mb-4 ring-4 ring-green-100">
                                <h3 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($pegawai['nama']) ?></h3>
                                <p class="text-green-600 font-semibold mb-3"><?= htmlspecialchars($pegawai['jabatan']) ?></p>
                                <p class="text-gray-500 text-sm flex-grow">
                                    <?= htmlspecialchars($pegawai['tentang']) ?>
                                </p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else : ?>
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                            <div class="text-gray-400 text-3xl">👥</div>
                        </div>
                        <p class="text-gray-500 text-lg">Data pegawai belum tersedia.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

<?php include 'footer.php'; ?>