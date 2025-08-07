<?php
// Koneksi ke database
include 'koneksi.php';

// Mengambil semua data dari tabel profil untuk digunakan di halaman ini
$profil_query = mysqli_query($koneksi, "SELECT jenis, isi FROM profil");
$profil_data = [];
while ($row = mysqli_fetch_assoc($profil_query)) {
    $profil_data[$row['jenis']] = $row['isi'];
}

// Mengambil data pimpinan dari tabel profil_pemimpin
$pemimpin_query = mysqli_query($koneksi, "SELECT * FROM profil_pemimpin LIMIT 1");
$pemimpin = mysqli_fetch_assoc($pemimpin_query);

// Mengambil gambar beranda dari database
$gambar_beranda_query = mysqli_query($koneksi, "SELECT nama_file FROM gambar_beranda ORDER BY id DESC LIMIT 1");
$gambar_beranda = mysqli_fetch_assoc($gambar_beranda_query);
$gambar_beranda_url = $gambar_beranda && $gambar_beranda['nama_file'] ? 'upload/gambar_beranda/' . $gambar_beranda['nama_file'] : 'gambar_beranda/UBS00415.JPG';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roudlotul Quran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fancybox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"/>
    <style>
        /* Image modal optimizations */
        .image-modal-overlay {
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }

        html {
            scroll-behavior: smooth;
        }
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
    document.getElementById("menu-toggle").addEventListener("click", function () {
        const menu = document.getElementById("mobile-menu");
        menu.classList.toggle("hidden");
    });
</script>

    <main>
<!-- Hero Section dengan Social Media Icons -->
<section class="relative h-screen overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0" style="background-image: url('<?= $gambar_beranda_url ?>'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-black/70"></div>
    </div>
    
    <!-- Main Content Container -->
    <div class="relative z-10 h-full">
        <div class="container mx-auto px-6 h-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center h-full">
                <!-- Content Area (Tengah-Kiri) -->
                <div class="lg:col-span-8 text-center lg:text-left text-white">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                        Selamat Datang di<br>
                        <span class="text-green-300">Pondok Pesantren</span><br>
                        Roudlotul Quran
                    </h1>
                    
                    <p class="mt-4 text-lg md:text-xl text-green-200 max-w-2xl lg:mx-0 mx-auto leading-relaxed">
                        <?= htmlspecialchars($profil_data['tag_line'] ?? 'Tagline belum diisi.') ?>
                    </p>
                    
                    <div class="mt-8 flex flex-col sm:flex-row lg:justify-start justify-center gap-4">
                        <a href="#profil-video" class="bg-white text-green-700 font-bold py-3 px-8 rounded-full hover:bg-gray-100 transition-transform hover:scale-105 shadow-lg">
                            Tentang Kami
                        </a>
                        <a href="#galeri" class="border-2 border-white text-white font-bold py-3 px-8 rounded-full hover:bg-white hover:text-green-700 transition-all hover:scale-105">
                            Lihat Galeri
                        </a>
                    </div>
                </div>
                
                <!-- Social Media Icons (Kanan) -->
                <div class="lg:col-span-4 flex lg:flex-col flex-row justify-center lg:justify-end items-center lg:items-end space-y-0 lg:space-y-6 space-x-4 lg:space-x-0 lg:pr-8">
                    <!-- YouTube -->
                    <a href="https://www.youtube.com/@MTSROUDLOTULQURAN" 
                       target="_blank"
                       class="social-icon floating-social bg-white/10 hover:bg-white/20 text-white p-4 rounded-full shadow-xl hover:shadow-2xl transition-all duration-300 group backdrop-blur-md"
                       aria-label="YouTube Channel">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                        <!-- Tooltip -->
                        <div class="absolute right-full top-1/2 transform -translate-y-1/2 mr-3 bg-black text-white px-3 py-1 rounded-lg text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap hidden lg:block">
                            YouTube Channel
                            <div class="absolute left-full top-1/2 transform -translate-y-1/2 w-0 h-0 border-l-4 border-l-black border-t-4 border-t-transparent border-b-4 border-b-transparent"></div>
                        </div>
                    </a>
                    
                    <!-- TikTok -->
                    <a href="https://www.tiktok.com/@mtsroudlotulquran?_t=ZS-8yS8ESQSBPE&_r=1" 
                       target="_blank"
                       class="social-icon floating-social bg-white/10 hover:bg-white/20 text-white p-4 rounded-full shadow-xl hover:shadow-2xl transition-all duration-300 group backdrop-blur-md"
                       aria-label="TikTok">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                        </svg>
                        <!-- Tooltip -->
                        <div class="absolute right-full top-1/2 transform -translate-y-1/2 mr-3 bg-black text-white px-3 py-1 rounded-lg text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap hidden lg:block">
                            TikTok
                            <div class="absolute left-full top-1/2 transform -translate-y-1/2 w-0 h-0 border-l-4 border-l-black border-t-4 border-t-transparent border-b-4 border-b-transparent"></div>
                        </div>
                    </a>
                    
                    <!-- Instagram -->
                    <a href="https://www.instagram.com/mtsroudlotulquran/" 
                       target="_blank"
                       class="social-icon floating-social bg-white/10 hover:bg-white/20 text-white p-4 rounded-full shadow-xl hover:shadow-2xl transition-all duration-300 group backdrop-blur-md"
                       aria-label="Instagram">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919C8.416 2.175 8.796 2.163 12 2.163M12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947C23.728 2.699 21.356.273 16.948.073 15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/>
                        </svg>
                        <!-- Tooltip -->
                        <div class="absolute right-full top-1/2 transform -translate-y-1/2 mr-3 bg-black text-white px-3 py-1 rounded-lg text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap hidden lg:block">
                            Instagram
                            <div class="absolute left-full top-1/2 transform -translate-y-1/2 w-0 h-0 border-l-4 border-l-black border-t-4 border-t-transparent border-b-4 border-b-transparent"></div>
                        </div>
                    </a>


                </div>
            </div>
        </div>
    </div>
</section>

      <section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <?php
            $data_statistik = mysqli_query($koneksi, "SELECT * FROM info_statistik ORDER BY id ASC LIMIT 4");
            while ($row = mysqli_fetch_assoc($data_statistik)) :
                $label = strtolower($row['label']);
                $icon_path = '';

                switch ($label) {
                    case 'siswa aktif':
                        $icon_path = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />';
                        break;
                    case 'akreditasi':
                        $icon_path = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />';
                        break;
                    case 'jumlah kelas':
                        $icon_path = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />';
                        break;
                    case 'guru & staff':
                        $icon_path = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />';
                        break;
                    default:
                        $icon_path = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
                        break;
                }
            ?>
            <div class="flex flex-col items-center">
                <div class="bg-green-100 text-green-600 rounded-full p-4 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <?= $icon_path ?>
                    </svg>
                </div>
                <p class="text-4xl font-bold text-gray-800"><?= htmlspecialchars($row['nilai']) ?></p>
                <p class="text-gray-500"><?= htmlspecialchars($row['label']) ?></p>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>


        <section id="profil-video" class="py-20 bg-green-100">
            <div class="container mx-auto px-6">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-800">Profil Pondok</h2>
                    <p class="mt-2 text-gray-600">Mengenal lebih dekat pimpinan dan visi kehidupan di Pondok Pesantren.</p>
                </div>
                <?php if ($pemimpin): ?>
                 <div class="text-center mb-12">
                    <div class="flex justify-center">
                        <img src="upload/gambar_pegawai/<?= htmlspecialchars($pemimpin['foto']) ?>" alt="Foto <?= htmlspecialchars($pemimpin['nama']) ?>" class="w-40 h-40 object-cover rounded-full shadow-md border-4 border-white">
                    </div>
                    <h3 class="mt-6 text-xl font-semibold text-gray-800"><?= htmlspecialchars($pemimpin['nama']) ?></h3>
                    <p class="text-gray-500 mb-4"><?= htmlspecialchars($pemimpin['jabatan']) ?></p>
                    <blockquote class="italic text-green-700 text-lg font-medium">"<?= htmlspecialchars($pemimpin['slogan']) ?>"</blockquote>
                </div>
                <?php else: ?>
                    <p class="text-center text-gray-500">Data pimpinan belum tersedia.</p>
                <?php endif; ?>
                    
                <?php
                $profil = mysqli_query($koneksi, "SELECT isi FROM profil WHERE jenis = 'sambutan_kepala' LIMIT 1");
                $data_profil = mysqli_fetch_assoc($profil);
                $link = $data_profil['isi'] ?? '';

                // Ekstrak ID dari link YouTube
                parse_str(parse_url($link, PHP_URL_QUERY), $ytParams);
                $videoID = $ytParams['v'] ?? null;
                if ($videoID) {
                echo '<div class="flex justify-center">';
                echo '<div class="w-full max-w-2xl aspect-video">';
                echo "<iframe class='w-full h-full' src='https://www.youtube.com/embed/$videoID' frameborder='0' allowfullscreen></iframe>";
                echo '</div></div>';} else {echo "<p class='text-gray-500'>Link video tidak valid atau belum diisi.</p>";}
                ?>
            </div>
        </section>

       <section id="sejarah" class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800">Sejarah Pondok</h2>
            <p class="mt-2 text-gray-600">Mengenal perjalanan dan perkembangan Pondok Pesantren Roudlotul Quran.</p>
        </div>
        <div class="max-w-4xl mx-auto">
            <div class="bg-green-50 p-8 rounded-lg shadow-sm">
                <div class="flex items-center justify-center mb-6">
                    <div class="bg-green-100 text-green-600">
                        <!-- Icon or image can be placed here if needed -->
                    </div>
                </div>
                <div class="text-gray-700 text-lg leading-relaxed max-w-prose mx-auto overflow-wrap break-words text-justify">
                    <?php if (!empty($profil_data['sejarah'])): ?>
                        <?php 
                        $sejarah_paragraphs = explode("\n", $profil_data['sejarah']);
                        foreach ($sejarah_paragraphs as $paragraph) {
                            $paragraph = trim($paragraph);
                            if (!empty($paragraph)) {
                                echo '<p class="mb-4">' . htmlspecialchars($paragraph) . '</p>';
                            }
                        }
                        ?>
                    <?php else: ?>
                        <p class="text-center text-gray-500 italic">Sejarah pondok pesantren belum diisi. Silakan hubungi administrator untuk informasi lebih lanjut.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>


        <section id="visi-misi" class="py-20 bg-gray-50">
            <div class="container mx-auto px-6">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-800">Visi & Misi</h2>
                    <p class="mt-2 text-gray-600">Landasan dan tujuan Pondok Pesantren Roudlotul Quran.</p>
                </div>
                <div class="grid md:grid-cols-2 gap-12 items-start">
                    <div class="bg-green-50 p-8 rounded-lg shadow-sm">
                        <h3 class="text-2xl font-semibold text-gray-800 mb-4 text-center">Visi</h3>
                        <p class="text-gray-600 text-center">
                           "<?= htmlspecialchars($profil_data['visi'] ?? 'Visi belum diisi.') ?>"
                        </p>
                    </div>
                    <div class="bg-green-50 p-8 rounded-lg shadow-sm">
                        <h3 class="text-2xl font-semibold text-gray-800 mb-4 text-center">Misi</h3>
                        <div class="text-gray-600 text-left space-y-2">
                           <?php
                           $misi_items = !empty($profil_data['misi']) ? explode("\n", $profil_data['misi']) : [];
                           if (!empty($misi_items) && (count($misi_items) > 1 || !empty(trim($misi_items[0])))) {
                               echo '<ul class="list-disc list-inside space-y-2">';
                               foreach ($misi_items as $item) {
                                   if (!empty(trim($item))) {
                                       echo '<li>' . htmlspecialchars(trim($item)) . '</li>';
                                   }
                               }
                               echo '</ul>';
                           } else {
                               echo '<p class="text-center">Misi belum diisi.</p>';
                           }
                           ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="artikel" class="py-20 bg-white">
             <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold text-gray-800">Berita Terbaru</h2>
                <p class="mt-2 text-gray-600 max-w-2xl mx-auto">Ikuti perkembangan dan kegiatan terbaru dari Pondok Pesantren Roudlotul Quran</p>
                <div class="grid md:grid-cols-2 gap-8 mt-12 text-left">
                    <?php
                    $artikel_query = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_post DESC LIMIT 2");
                    if (mysqli_num_rows($artikel_query) > 0) :
                        while ($artikel = mysqli_fetch_assoc($artikel_query)) :
                    ?>
                    <a href="berita_detail.php?id=<?= $artikel['id'] ?>" class="text-green-600 hover:text-green-800 font-bold">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden transform hover:-translate-y-2 transition-transform duration-300">
                        <img src="upload/gambar_berita/<?= htmlspecialchars($artikel['gambar_utama']) ?>" class="h-56 w-full object-cover" alt="Gambar Artikel">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2 text-gray-800"><?= htmlspecialchars($artikel['judul']) ?></h3>
                            <p class="text-gray-500 text-sm mb-4">Diposting: <?= date('d F Y', strtotime($artikel['tanggal_post'])) ?></p>
                            Baca Selengkapnya →
                        </div>
                    </div>
                    </a>
                    <?php
                        endwhile;
                    else:
                        echo '<p class="text-center col-span-full text-gray-500">Belum ada artikel.</p>';
                    endif;
                    ?>
                </div>
                <div class="mt-12">
                    <a href="berita.php" class="bg-green-600 text-white font-bold py-3 px-8 rounded-full hover:bg-green-700 transition-colors">
                        Lihat Semua Berita
                    </a>
                </div>
            </div>
        </section>

        <section id="galeri" class="py-20 bg-gray-50">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold text-gray-800">Galeri Kegiatan</h2>
                <p class="mt-2 text-gray-600 max-w-2xl mx-auto">Momen dan kegiatan yang terdokumentasi di Pondok Pesantren Roudlotul Quran.</p>
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-12">
                    <?php
                    $galeri_query = mysqli_query($koneksi, "SELECT * FROM galeri ORDER BY tanggal_post DESC LIMIT 6");
                    if ($galeri_query && mysqli_num_rows($galeri_query) > 0) :
                        while ($foto = mysqli_fetch_assoc($galeri_query)) :
                    ?>
                    <div class="rounded-lg overflow-hidden shadow-md transform hover:scale-105 transition-transform duration-300">
                        <a href="upload/gambar_galeri/<?= htmlspecialchars($foto['file_path']) ?>" 
                           data-fancybox="gallery-home" 
                           data-caption="<?= htmlspecialchars($foto['deskripsi']) ?>">
                             <img src="upload/gambar_galeri/<?= htmlspecialchars($foto['file_path']) ?>" 
                                  alt="<?= htmlspecialchars($foto['deskripsi']) ?>" 
                                  class="w-full h-full object-cover aspect-square">
                        </a>
                    </div>
                    <?php
                        endwhile;
                    else:
                        echo '<p class="text-center col-span-full text-gray-500">Belum ada foto di galeri.</p>';
                    endif;
                    ?>
                </div>
                <div class="mt-12">
                    <a href="galeri.php" class="bg-green-600 text-white font-bold py-3 px-8 rounded-full hover:bg-green-700 transition-colors">
                        Lihat Semua Galeri
                    </a>
                </div>
            </div>
        </section>
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
                        <span class="text-white font-semibold">Semua hak dilindungi undang-undang.</span><br>
                        <a href="" class="text-white font-semibold">Dibuat oleh Rehan, Ferdie, dan Nadhif.❤️</a>
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

</script>

</body>
</html>