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
    <title>Profil Pegawai - Pondok Pesantren Roudlotul Quran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
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
            <img src="upload/logo/STK-20250718-WA0016.png" alt="Roudlotul Quran" class="h-20 w-20">
            <span class="text-xl font-bold text-white leading-tight">Pondok Pesantren <br> Roudlotul Quran</span>
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
        <a href="index.php" class="block py-2 text-green-100 hover:text-white">Beranda</a>
        <a href="profil.php" class="block py-2 text-white font-semibold">Profil</a>
        <a href="berita.php" class="block py-2 text-green-100 hover:text-white">Berita</a>
        <a href="prestasi.php" class="block py-2 text-green-100 hover:text-white">Prestasi</a>
        <a href="galeri.php" class="block py-2 text-green-100 hover:text-white">Galeri</a>
    </div>
</header>

    <main class="pt-24 pb-16 md:pt-32 md:pb-24">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 animate-fadeInDown">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                    Profil Guru & <span class="text-green-600">Staff</span>
                </h1>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Tenaga pendidik dan staf yang berdedikasi di Pondok Pesantren Roudlotul Quran, siap membimbing santri menuju kesuksesan dunia dan akhirat.
                </p>
                <div class="w-24 h-1 bg-gradient-to-r from-green-400 to-green-600 rounded-full mx-auto mt-6"></div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12 animate-fadeInUp">
                <?php if ($pegawai_result && mysqli_num_rows($pegawai_result) > 0) : ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php while ($pegawai = mysqli_fetch_assoc($pegawai_result)) : ?>
                            <div class="bg-gray-50 rounded-xl p-8 text-center flex flex-col items-center transition-all duration-300 hover:shadow-xl hover:bg-white hover:-translate-y-1">
                                <img src="upload/<?= htmlspecialchars($pegawai['foto']) ?>" alt="Foto <?= htmlspecialchars($pegawai['nama']) ?>" class="w-28 h-28 rounded-full object-cover mb-4 ring-4 ring-green-100">
                                <h3 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($pegawai['nama']) ?></h3>
                                <p class="text-green-600 font-semibold mb-3"><?= htmlspecialchars($pegawai['jabatan']) ?></p>
                                <p class="text-gray-500 text-sm flex-grow">
                                    <?= htmlspecialchars($pegawai['pengalaman_kerja']) ?>
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

    <footer class="bg-gradient-to-br from-green-800 via-green-700 to-green-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
       <div class="absolute top-0 left-0 w-full h-full" style="background-image: url('grain.svg');"></div>
    </div>
    
    <div class="container mx-auto px-6 py-12 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
            <div class="lg:col-span-2">
                <div class="flex items-center mb-6">
                    <img src="upload/logo/STK-20250718-WA0016.png" alt="Roudlotul Quran" class="h-16 w-16 mr-4 rounded-full border-2 border-white/20">
                    <div>
                        <h4 class="text-white text-xl font-bold">Pondok Pesantren</h4>
                        <h4 class="text-green-200 text-lg font-semibold">Roudlotul Quran</h4>
                    </div>
                </div>

                <p class="text-green-100 leading-relaxed mb-6">
                    <?= htmlspecialchars($profil_data['tag_line'] ?? 'Pondok Pesantren Roudlotul Quran berkomitmen untuk mendidik generasi muda Islami yang berakhlak mulia, berilmu pengetahuan, dan berjiwa pemimpin.') ?>
                </p>
                
                <div class="flex space-x-4">
                    <a href="#" class="bg-white/10 hover:bg-white/20 p-3 rounded-full transition-all duration-300 hover:scale-110" aria-label="YouTube">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21.582,6.186c-0.23-0.86-0.908-1.538-1.768-1.768C18.254,4,12,4,12,4S5.746,4,4.186,4.418 c-0.86,0.23-1.538,0.908-1.768,1.768C2,7.746,2,12,2,12s0,4.254,0.418,5.814c0.23,0.86,0.908,1.538,1.768,1.768 C5.746,20,12,20,12,20s6.254,0,7.814-0.418c0.861-0.23,1.538-0.908,1.768-1.768C22,16.254,22,12,22,12S22,7.746,21.582,6.186z M10,15.464V8.536L16,12L10,15.464z"></path></svg>
                    </a>
                    <a href="#" class="bg-white/10 hover:bg-white/20 p-3 rounded-full transition-all duration-300 hover:scale-110" aria-label="TikTok">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M16.6,5.82s.51.5,0,0A4.278,4.278,0,0,1,15.54,3H12.45V15.4a2.592,2.592,0,0,1-2.59,2.59c-1.43,0-2.6-1.16-2.6-2.6s1.17-2.6,2.6-2.6c.2,0,.39.02.58.06V10.4a4.832,4.832,0,0,0-4.83,4.83c0,2.66,2.17,4.83,4.83,4.83s4.83-2.17,4.83-4.83V8.18H19.3V5.82H16.6Z"></path></svg>
                    </a>
                    <a href="#" class="bg-white/10 hover:bg-white/20 p-3 rounded-full transition-all duration-300 hover:scale-110" aria-label="Instagram">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12,2.163c3.204,0,3.584,0.012,4.85,0.07c3.252,0.148,4.771,1.691,4.919,4.919c0.058,1.265,0.069,1.645,0.069,4.849 c0,3.205-0.012,3.584-0.069,4.849c-0.149,3.225-1.664,4.771-4.919,4.919c-1.266,0.058-1.644,0.07-4.85,0.07 c-3.204,0-3.584-0.012-4.849-0.07c-3.26-0.149-4.771-1.699-4.919-4.92c-0.058-1.265-0.07-1.644-0.07-4.849 c0-3.204,0.013-3.583,0.07-4.849c0.149-3.227,1.664-4.771,4.919-4.919C8.416,2.175,8.796,2.163,12,2.163 M12,0 C8.741,0,8.333,0.014,7.053,0.072C2.699,0.272,0.273,2.699,0.073,7.053C0.014,8.333,0,8.741,0,12c0,3.259,0.014,3.668,0.072,4.948 c0.2,4.358,2.618,6.78,6.98,6.98c1.281,0.058,1.689,0.072,4.948,0.072c3.259,0,3.668-0.014,4.948-0.072 c4.354-0.2,6.782-2.618,6.979-6.98c0.059-1.28,0.073-1.689,0.073-4.948c0-3.259-0.014-3.667-0.072-4.947 C21.382,2.699,18.956,0.272,14.6,0.072C13.317,0.014,12.91,0,12,0L12,0z M12,5.462c-3.6,0-6.538,2.939-6.538,6.538 s2.939,6.538,6.538,6.538s6.538-2.939,6.538-6.538S15.6,5.462,12,5.462z M12,16.338c-2.389,0-4.338-1.949-4.338-4.338 c0-2.389,1.949-4.338,4.338-4.338s4.338,1.949,4.338,4.338C16.338,14.389,14.389,16.338,12,16.338z M18.406,6.406 c-0.796,0-1.441,0.645-1.441,1.44s0.645,1.44,1.441,1.44c0.795,0,1.439-0.645,1.439-1.44S19.201,6.406,18.406,6.406z"></path></svg>
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
                            <p class="text-green-100 text-sm leading-relaxed">Jl. Raya Pesantren No. 123<br>Sidoarjo, Jawa Timur 61234</p>
                        </div>
                    </li>
                    <li class="flex items-center group">
                        <div class="bg-white/10 p-2 rounded-lg mr-3 group-hover:bg-white/20 transition-colors duration-300">
                            <svg class="w-4 h-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <p class="text-green-100">+62 31 1234 5678</p>
                    </li>
                    <li class="flex items-center group">
                        <div class="bg-white/10 p-2 rounded-lg mr-3 group-hover:bg-white/20 transition-colors duration-300">
                            <svg class="w-4 h-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-green-100">info@roudlotulquran.ponpes.id</p>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="pt-8 border-t border-white/10">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-4 md:mb-0">
                    <p class="text-green-100 text-sm">
                        © <?= date('Y') ?> Pondok Pesantren Roudlotul Quran. 
                        <span class="text-white font-semibold">Semua hak dilindungi undang-undang.</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

    <script>
        document.getElementById("menu-toggle").addEventListener("click", function () {
            const menu = document.getElementById("mobile-menu");
            menu.classList.toggle("hidden");
        });
    </script>
</body>
</html>