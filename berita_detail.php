<?php
include 'koneksi.php';

// Cek apakah ID ada di URL
if (!isset($_GET['id'])) {
    // Redirect ke halaman berita jika tidak ada ID
    header('Location: berita.php');
    exit;
}

$id = intval($_GET['id']);

// Ambil data untuk berita yang sedang dibaca
$data_utama = mysqli_query($koneksi, "SELECT * FROM berita WHERE id = $id");
$berita = mysqli_fetch_assoc($data_utama);

// Jika berita dengan ID tersebut tidak ditemukan, redirect
if (!$berita) {
    header('Location: berita.php');
    exit;
}

// Ambil 4 berita terbaru lainnya (selain yang sedang dibaca) untuk sidebar
$query_lainnya = "SELECT id, judul, gambar_utama, tanggal_post FROM berita WHERE id != $id ORDER BY tanggal_post DESC LIMIT 4";
$berita_lainnya = mysqli_query($koneksi, $query_lainnya);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="upload/logo/Logo_MTS.png" type="image/png">
    <title>Madrasah Tsanawiyah Roudlotul Quran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-gray-50 min-h-screen font-sans">

<body class="bg-gray-50 min-h-screen font-sans">
<header class="bg-green-700 sticky top-0 z-50 shadow-lg">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <a href="index.php" class="flex items-center gap-3">
            <img src="upload/logo/Logo_MTS.png" alt="Roudlotul Quran" class="h-20 w-20">
            <span class="text-xl font-bold text-white leading-tight">Madrasah Tsanawiyah <br> Roudlotul Quran</span>
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
        <a href="index.php" class="block py-2 text-green-100 hover:text-white">Beranda</a>
        <a href="profil.php" class="block py-2 text-green-100 hover:text-white">Profil</a>
        <a href="berita.php" class="block py-2 text-white font-semibold">Berita</a>
        <a href="prestasi.php" class="block py-2 text-green-100 hover:text-white">Prestasi</a>
        <a href="galeri.php" class="block py-2 text-green-100 hover:text-white">Galeri</a>
    </div>
</header>

<script>
    document.getElementById("menu-toggle").addEventListener("click", function () {
        document.getElementById("mobile-menu").classList.toggle("hidden");
    });
</script>

<div class="container mx-auto py-8 lg:py-16 px-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-12">

        <main class="lg:col-span-2 bg-white rounded-lg shadow-lg p-6 md:p-8">
            <h1 class="text-2xl sm:text-4xl font-bold text-gray-900 mb-4 leading-tight"><?= htmlspecialchars($berita['judul']) ?></h1>
            
            <p class="text-sm text-gray-500 mb-6">
                Diposting pada <?= date('d F Y', strtotime($berita['tanggal_post'])) ?> oleh <?= htmlspecialchars($berita['penulis']) ?>
            </p>

            <?php if (!empty($berita['gambar_utama'])): ?>
                <div class="mb-8">
                    <img src="upload/gambar_berita/<?= htmlspecialchars($berita['gambar_utama']) ?>" alt="Gambar: <?= htmlspecialchars($berita['judul']) ?>"
                         class="rounded-lg w-full max-h-[500px] object-cover shadow-md" />
                </div>
            <?php endif; ?>

            <article class="prose max-w-none text-justify text-base sm:text-lg leading-relaxed text-gray-800">
                <?= nl2br($berita['isi']) // Menggunakan nl2br tanpa htmlspecialchars agar tag HTML dari editor bisa render ?>
            </article>

            <?php if (!empty($berita['video_youtube'])): ?>
                <?php
                parse_str(parse_url($berita['video_youtube'], PHP_URL_QUERY), $ytParams);
                $videoID = $ytParams['v'] ?? null;
                ?>
                <?php if ($videoID): ?>
                    <div class="mt-10">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Tonton Video</h3>
                        <div class="aspect-video">
                            <iframe class="w-full h-full rounded-lg shadow-md" src="https://www.youtube.com/watch?v=XpmeVNxZ-Ks&list=RDpp4YQPykBMM&index=11&ab_channel=IlleniumVEVO<?= $videoID ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </main>

        <aside class="lg:col-span-1 mt-12 lg:mt-0">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-28">
                <h3 class="text-xl font-bold text-gray-900 border-b pb-3 mb-4">Berita Lainnya</h3>
                <div class="space-y-5">
                    <?php if (mysqli_num_rows($berita_lainnya) > 0): ?>
                        <?php while ($lainnya = mysqli_fetch_assoc($berita_lainnya)): ?>
                            <a href="berita_detail.php?id=<?= $lainnya['id'] ?>" class="group flex items-center gap-4">
                                <img src="upload/gambar_berita/<?= htmlspecialchars($lainnya['gambar_utama']) ?>" alt="<?= htmlspecialchars($lainnya['judul']) ?>" class="w-20 h-20 rounded-md object-cover flex-shrink-0">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 group-hover:text-green-600 transition-colors leading-tight">
                                        <?= htmlspecialchars($lainnya['judul']) ?>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1"><?= date('d M Y', strtotime($lainnya['tanggal_post'])) ?></p>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-sm text-gray-500">Tidak ada berita lainnya.</p>
                    <?php endif; ?>
                </div>
                 <div class="mt-8">
                    <a href="berita.php" class="w-full text-center inline-block bg-green-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                        Lihat Semua Berita
                    </a>
                </div>
            </div>
        </aside>

    </div>
</div>

<footer class="bg-gradient-to-br from-green-800 via-green-700 to-green-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-full h-full" style="background-image: url('grain.svg');"></div>
    </div>
    
    <div class="container mx-auto px-6 py-12 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
            <div class="lg:col-span-2">
                <div class="flex items-center mb-6">
                    <img src="upload/logo/Logo_MTS.png" alt="Roudlotul Quran" class="h-16 w-16 mr-4 rounded-full border-2 border-white/20">
                    <div>
                        <h4 class="text-white text-xl font-bold">Madrasah Tsanawiyah</h4>
                        <h4 class="text-green-200 text-lg font-semibold">Roudlotul Quran</h4>
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
                        © <?= date('Y') ?> Madrasah Tsanawiyah Roudlotul Quran. 
                        <span class="text-white font-semibold">Semua hak dilindungi undang-undang.</span><br>
                        <a href="https://hiimistis.carrd.co/" class="text-white font-semibold">Dibuat oleh Rehan, Ferdie, dan Nadhif.❤️</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>      

</body>
</html>