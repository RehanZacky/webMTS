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

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roudlotul Quran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
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
        <section class="relative h-screen flex items-center justify-center text-white text-center overflow-hidden">
        <div class="absolute inset-0 z-0" style="background-image: url('gambar_beranda/UBS00415.JPG'); background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-black/70"></div>
        </div>
        <div class="relative z-10 max-w-3xl px-4">
            <h1 class="text-5xl md:text-6xl font-extrabold leading-tight">Selamat Datang di Pondok Pesantren Roudlotul Quran</h1>
            
            <p class="mt-4 text-lg md:text-xl text-green-200"><?= htmlspecialchars($profil_data['tag_line'] ?? 'Tagline belum diisi.') ?></p>
            
            <div class="mt-8 flex justify-center gap-4">
                <a href="#profil-video" class="bg-white text-green-700 font-bold py-3 px-8 rounded-full hover:bg-gray-100 transition-transform hover:scale-105">Tentang Kami</a>
                <a href="#galeri" class="border-2 border-white text-white font-bold py-3 px-8 rounded-full hover:bg-white hover:text-green-700 transition-all hover:scale-105">Lihat Galeri</a>
            </div>
        </div>
        </section>

        <section class="py-16 bg-white">
            <div class="container mx-auto px-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <?php
                    $data_statistik = mysqli_query($koneksi, "SELECT * FROM info_statistik ORDER BY id ASC LIMIT 4");
                    while ($row = mysqli_fetch_assoc($data_statistik)) :
                    ?>
                    <div class="flex flex-col items-center">
                        <div class="bg-green-100 text-green-600 rounded-full p-4 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 14l9-5-9-5-9 5 9 5z" /><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-9.998 12.078 12.078 0 01.665-6.479L12 14z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-9.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222 4 2.222V20M1 12l11 6 9-6" /></svg>
                        </div>
                        <p class="text-4xl font-bold text-gray-800"><?= htmlspecialchars($row['nilai']) ?></p>
                        <p class="text-gray-500"><?= htmlspecialchars($row['label']) ?></p>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>

        <section id="profil-video" class="py-20 bg-green-50">
            <div class="container mx-auto px-6">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-800">Profil Pondok</h2>
                    <p class="mt-2 text-gray-600">Mengenal lebih dekat pimpinan dan visi kehidupan di Pondok Pesantren.</p>
                </div>
                <?php if ($pemimpin): ?>
                 <div class="text-center mb-12">
                    <div class="flex justify-center">
                        <img src="upload/<?= htmlspecialchars($pemimpin['foto']) ?>" alt="Foto <?= htmlspecialchars($pemimpin['nama']) ?>" class="w-40 h-40 object-cover rounded-full shadow-md border-4 border-white">
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

        <section id="visi-misi" class="py-20 bg-white">
            <div class="container mx-auto px-6">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-800">Visi & Misi</h2>
                    <p class="mt-2 text-gray-600">Landasan dan tujuan Pondok Pesantren Roudlotul Quran.</p>
                </div>
                <div class="grid md:grid-cols-2 gap-12 items-start">
                    <div class="bg-gray-50 p-8 rounded-lg shadow-sm">
                        <h3 class="text-2xl font-semibold text-gray-800 mb-4 text-center">Visi</h3>
                        <p class="text-gray-600 text-center">
                           "<?= htmlspecialchars($profil_data['visi'] ?? 'Visi belum diisi.') ?>"
                        </p>
                    </div>
                    <div class="bg-gray-50 p-8 rounded-lg shadow-sm">
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

        <section id="artikel" class="py-20 bg-gray-50">
             <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold text-gray-800">Berita Terbaru</h2>
                <p class="mt-2 text-gray-600 max-w-2xl mx-auto">Ikuti perkembangan dan kegiatan terbaru dari Pondok Pesantren Roudlotul Quran</p>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mt-12 text-left">
                    <?php
                    $artikel_query = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_post DESC LIMIT 3");
                    if (mysqli_num_rows($artikel_query) > 0) :
                        while ($artikel = mysqli_fetch_assoc($artikel_query)) :
                    ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden transform hover:-translate-y-2 transition-transform duration-300">
                        <img src="upload/gambar_berita/<?= htmlspecialchars($artikel['gambar_utama']) ?>" class="h-56 w-full object-cover" alt="Gambar Artikel">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2 text-gray-800"><?= htmlspecialchars($artikel['judul']) ?></h3>
                            <p class="text-gray-500 text-sm mb-4">Diposting: <?= date('d F Y', strtotime($artikel['tanggal_post'])) ?></p>
                            <a href="berita_detail.php?id=<?= $artikel['id'] ?>" class="text-green-600 hover:text-green-800 font-bold">Baca Selengkapnya →</a>
                        </div>
                    </div>
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

        <section id="galeri" class="py-20 bg-white">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold text-gray-800">Galeri Kegiatan</h2>
                <p class="mt-2 text-gray-600 max-w-2xl mx-auto">Momen dan kegiatan yang terdokumentasi di Pondok Pesantren Roudlotul Quran.</p>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-12">
                    <?php
                    $galeri_query = mysqli_query($koneksi, "SELECT * FROM gambar ORDER BY tanggal_upload DESC LIMIT 8");
                    if ($galeri_query && mysqli_num_rows($galeri_query) > 0) :
                        while ($foto = mysqli_fetch_assoc($galeri_query)) :
                    ?>
                    <div class="rounded-lg overflow-hidden shadow-md transform hover:scale-105 transition-transform duration-300">
                        <a href="<?= htmlspecialchars($foto['file_path']) ?>" data-fancybox="gallery" data-caption="<?= htmlspecialchars($foto['deskripsi']) ?>">
                             <img src="upload/g<?= htmlspecialchars($foto['file_path']) ?>" alt="<?= htmlspecialchars($foto['deskripsi']) ?>" class="w-full h-full object-cover aspect-square">
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
        <<div class="absolute top-0 left-0 w-full h-full" style="background-image: url('grain.svg');"></div>
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
</body>
</html>