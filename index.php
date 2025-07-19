<?php
// Koneksi ke database Anda tetap tidak berubah
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roudlotul Quran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Menambahkan style untuk scroll yang lebih halus */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="bg-gray-50">

   <!-- NAVBAR UTAMA -->
<header class="bg-green-700 sticky top-0 z-50 shadow-lg">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <a href="index.php" class="flex items-center gap-3">
            <img src="upload\STK-20250718-WA0016.png" alt="Roudlotul Quran" class="h-20 w-20">
            <span class="text-xl font-bold text-white leading-tight">Pondok Pesantren <br> Roudlotul Quran</span>
        </a>

        <nav class="hidden md:flex items-center space-x-8">
            <a href="index.php" class="text-white font-bold">Beranda</a>
            <a href="profil.php" class="text-green-100 hover:text-white font-semibold">Profil</a>
            <a href="berita.php" class="text-green-100 hover:text-white font-semibold">Berita</a>
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
        <a href="galeri.php" class="block py-2 text-green-100 hover:text-white">Galeri</a>
    </div>
</header>

<!-- SCRIPT UNTUK MOBILE MENU -->
<script>
    document.getElementById("menu-toggle").addEventListener("click", function () {
        const menu = document.getElementById("mobile-menu");
        menu.classList.toggle("hidden");
    });
</script>

    <!-- HOMEPAGE -->
    <main>
        <section class="relative h-screen flex items-center justify-center text-white text-center overflow-hidden">
        <div class="absolute inset-0 z-0" style="background-image: url('gambar_beranda/UBS00415.JPG'); background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-black/70"></div>
        </div>
        <div class="relative z-10 max-w-3xl px-4">
            <h1 class="text-5xl md:text-6xl font-extrabold leading-tight">Selamat Datang di Pondok Pesantren Roudlotul Quran</h1>
            <p class="mt-4 text-lg md:text-xl text-green-200">Mewujudkan sumber daya manusia yang peduli dan berbudaya ramah lingkungan melalui kegiatan madrasah yang berkesinambungan.</p>
            <div class="mt-8 flex justify-center gap-4">
                <a href="#sambutan" class="bg-white text-green-700 font-bold py-3 px-8 rounded-full hover:bg-gray-100 transition-transform hover:scale-105">Tentang Kami</a>
                <a href="#galeri" class="border-2 border-white text-white font-bold py-3 px-8 rounded-full hover:bg-white hover:text-green-700 transition-all hover:scale-105">Lihat Galeri</a>
            </div>
        </div>
        </section>

        <section class="py-16 bg-white">
            <div class="container mx-auto px-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <?php
                    // PHP untuk mengambil data statistik, sama seperti kode asli Anda
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

                 <div class="text-center">
                <div class="flex justify-center">
                    <img src="https://placehold.co/200x200/16a34a/ffffff?text=KH" alt="Pimpinan" class="w-40 h-40 object-cover rounded-full shadow-md border-4 border-white">
                </div>
                <h3 class="mt-6 text-xl font-semibold text-gray-800">KH. Muhammad Rifqi, Lc</h3>
                <p class="text-gray-500 mb-4">Wakil Pengasuh Roudlotul Quran</p>
                <blockquote class="italic text-green-700 text-lg font-medium">"Ilmu tanpa adab adalah kesesatan, dan adab tanpa ilmu adalah kebodohan."</blockquote>
            </div>
        </div>
    </div>
                    
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
        echo '</div></div>';
    } else {
        echo "<p class='text-gray-500'>Link video tidak valid atau belum diisi.</p>";
    }
    ?>
                    
                </div>
            </div>
        </section>

        <section id="visi-misi" class="py-20 bg-white">
            <div class="container mx-auto px-6">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-800">Visi & Misi</h2>
                    <p class="mt-2 text-gray-600">Landasan dan tujuan Pondok Pesantren Al-Mujahidin.</p>
                </div>
                <div class="grid md:grid-cols-2 gap-12 items-start">
                    <div class="bg-gray-50 p-8 rounded-lg shadow-sm">
                        <h3 class="text-2xl font-semibold text-gray-800 mb-4 text-center">Visi</h3>
                        <p class="text-gray-600 text-center">
                            "Mewujudkan sumber daya manusia yang berakhlak mulia, unggul dalam prestasi, peduli, dan berbudaya ramah lingkungan."
                        </p>
                    </div>
                    <div class="bg-gray-50 p-8 rounded-lg shadow-sm">
                        <h3 class="text-2xl font-semibold text-gray-800 mb-4 text-center">Misi</h3>
                        <ul class="list-disc list-inside space-y-3 text-gray-600">
                            <li>Menyelenggarakan pendidikan formal dan non-formal yang berkualitas berbasis nilai-nilai Islam.</li>
                            <li>Membina santri menjadi pribadi yang beriman, bertaqwa, dan berakhlak mulia sesuai Al-Quran dan Sunnah.</li>
                            <li>Mengembangkan potensi santri di bidang akademik, non-akademik, dan kewirausahaan untuk berdaya saing global.</li>
                            <li>Menanamkan kesadaran dan kepedulian terhadap kelestarian lingkungan hidup melalui kegiatan yang nyata dan berkelanjutan.</li>
                            <li>Membangun jiwa kepemimpinan yang amanah dan bertanggung jawab pada diri santri.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- BERITA SECTION -->
        <section id="artikel" class="py-20 bg-white">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold text-gray-800">Berita Terbaru</h2>
                <p class="mt-2 text-gray-600 max-w-2xl mx-auto">Ikuti perkembangan dan kegiatan terbaru dari Pondok Pesantren Al-Mujahidin</p>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mt-12 text-left">
                    <?php
                    // PHP untuk mengambil artikel, sama seperti kode sebelumnya
                    $artikel_query = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_post DESC LIMIT 3");
                    if (mysqli_num_rows($artikel_query) > 0) :
                        while ($artikel = mysqli_fetch_assoc($artikel_query)) :
                    ?>
                    <div class="bg-gray-50 rounded-lg shadow-md overflow-hidden transform hover:-translate-y-2 transition-transform duration-300">
                        <img src="upload/<?= htmlspecialchars($artikel['gambar_utama']) ?>" class="h-56 w-full object-cover" alt="Gambar Artikel">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2 text-gray-800"><?= htmlspecialchars($artikel['judul']) ?></h3>
                            <p class="text-gray-500 text-sm mb-4">Diposting: <?= date('d F Y', strtotime($artikel['tanggal_post'])) ?></p>
                            <a href="berita_detail.php" class="text-green-600 hover:text-green-800 font-bold">Baca Selengkapnya →</a>
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

        <!-- GALERI SECTION -->
        <section id="galeri" class="py-20 bg-white">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold text-gray-800">Galeri Kegiatan</h2>
                <p class="mt-2 text-gray-600 max-w-2xl mx-auto">Momen dan kegiatan yang terdokumentasi di Pondok Pesantren Al-Mujahidin.</p>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-12">
                    <?php
                    // KODE DIPERBAIKI: Menggunakan tabel `gambar` dan kolom yang sesuai
                    $galeri_query = mysqli_query($koneksi, "SELECT * FROM gambar ORDER BY tanggal_upload DESC LIMIT 8");
                    if ($galeri_query && mysqli_num_rows($galeri_query) > 0) :
                        while ($foto = mysqli_fetch_assoc($galeri_query)) :
                    ?>
                    <div class="rounded-lg overflow-hidden shadow-md transform hover:scale-105 transition-transform duration-300">
                        <a href="<?= htmlspecialchars($foto['file_path']) ?>" data-fancybox="gallery" data-caption="<?= htmlspecialchars($foto['deskripsi']) ?>">
                             <img src="<?= htmlspecialchars($foto['file_path']) ?>" alt="<?= htmlspecialchars($foto['deskripsi']) ?>" class="w-full h-full object-cover aspect-square">
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

    <!-- FOOTER -->
    <footer class="bg-gray-800 text-gray-300">
        <div class="container mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-2">
                    <h4 class="text-white text-lg font-semibold mb-4">Pondok Pesantren Roudlotul Quran</h4>
                    <p class="text-gray-400">Pondok Pesantren Roudlotul Quran berkomitmen untuk mendidik generasi muda Islami yang berakhlak mulia, berilmu pengetahuan, dan berjiwa pemimpin dalam membangun peradaban yang berkualitas.</p>
                     <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-white"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M..."></path></svg></a>
                        <a href="#" class="text-gray-400 hover:text-white"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M..."></path></svg></a>
                        <a href="#" class="text-gray-400 hover:text-white"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M..."></path></svg></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-white text-lg font-semibold mb-4">Kontak Kami</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>Jl. Raya Pesantren No. 123, Sidoarjo, Jawa Timur 61234</li>
                        <li>+62 31 1234 5678</li>
                        <li>info@almujahidin.ac.id</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white text-lg font-semibold mb-4">Jam Operasional</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><span class="font-semibold">Senin - Jumat:</span> 07:00 - 16:00 WIB</li>
                        <li><span class="font-semibold">Sabtu - Minggu:</span> 07:00 - 12:00 WIB</li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-700 text-center text-gray-500 text-sm">
                <p>&copy; <?= date('Y') ?> Pondok Pesantren Roudlotul Quran. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>