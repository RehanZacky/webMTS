<?php
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita - Ponpes Roudlotul Quran</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white min-h-screen font-sans">

<!-- NAVBAR -->
<nav class="bg-white shadow-md fixed w-full z-50 top-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex-shrink-0 flex items-center space-x-2">
                <img class="h-10 w-auto" src="logo.png" alt="Logo"> <!-- Ganti dengan logo madrasah -->
                <span class="font-bold text-green-700">Ponpes Roudlotul Quran</span>
            </div>
            <div class="hidden md:flex space-x-6 items-center">
                <a href="index.php" class="text-green-700 hover:underline">Beranda</a>
                <a href="#Profil" class="text-green-700 hover:underline>Profil</a>
                <a href="#Berita" class="text-green-700 hover:underline">Berita</a>
                <a href="galeri.php" class="text-green-700 hover:underline">Galeri</a>
                <a href="#" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-600">PPDB Online</a>
            </div>
            <div class="md:hidden">
                <button id="menu-toggle" class="focus:outline-none">
                    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden py-2 space-y-2">
            <a href="index.php" class="block text-green-700 px-4">Beranda</a>
            <a href="#Profil" class="block px-4">Profil</a>
            <a href="#Berita" class="block px-4">Berita</a>
            <a href="galeri.php" class="block text-green-700 px-4">Galeri</a>
            <a href="#" class="block bg-green-700 text-white text-center mx-4 py-2 rounded">PPDB Online</a>
        </div>
    </div>
</nav>

<script>
    document.getElementById("menu-toggle").onclick = function () {
        const menu = document.getElementById("mobile-menu");
        menu.classList.toggle("hidden");
    };
</script>

<!-- HEADER -->
<header class="bg-gradient-to-b from-green-700 to-green-400 text-white text-center py-20 mt-16">
    <h1 class="text-3xl md:text-4xl font-bold px-4">Berita Seputar Kegiatan Madrasah, Prestasi Siswa, dan Artikel</h1>
</header>

<!-- SECTION: Kegiatan Madrasah -->
<section class="px-6 py-10">
    <h2 class="text-xl font-bold text-center text-white bg-green-600 py-2 rounded">Kegiatan Madrasah</h2>

    <div class="mt-6 overflow-x-auto whitespace-nowrap space-x-4 flex pb-4">
        <?php
        $berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_post DESC");
        $ada_kegiatan = false;
        while ($b = mysqli_fetch_assoc($berita)) :
            if (
                !empty($b['gambar_utama']) &&
                stripos($b['judul'], 'prestasi') === false && 
                stripos($b['isi'], 'prestasi') === false
            ):
                $ada_kegiatan = true;
        ?>
        <div class="inline-block w-80 flex-shrink-0 bg-white rounded shadow-md border border-gray-200">
            <a href="berita_detail.php?id=<?= $b['id'] ?>">
                <img src="upload/<?= $b['gambar_utama'] ?>" class="h-48 w-full object-cover rounded-t" alt="Gambar Berita">
            </a>
            <div class="p-4">
                <p class="text-sm text-gray-500 mb-1"><?= date('d M Y', strtotime($b['tanggal_post'])) ?> • <?= htmlspecialchars($b['penulis']) ?></p>
                <h3 class="text-md font-bold text-green-700 mb-1"><?= htmlspecialchars($b['judul']) ?></h3>
                <p class="text-sm text-justify text-gray-700"><?= nl2br(htmlspecialchars(substr($b['isi'], 0, 150))) ?>...</p>
                <a href="berita_detail.php?id=<?= $b['id'] ?>" class="text-sm text-green-600 hover:underline mt-2 inline-block">Baca Selengkapnya</a>
            </div>
        </div>
        <?php
            endif;
        endwhile;

        if (!$ada_kegiatan) {
            echo "<p class='text-center text-gray-600'>Belum ada berita kegiatan madrasah.</p>";
        }
        ?>
    </div>
</section>

<!-- SECTION: Prestasi Siswa -->
<section class="px-6 py-10">
    <h2 class="text-xl font-bold text-center text-white bg-green-500 py-2 rounded">Prestasi Siswa</h2>

    <div class="mt-6 overflow-x-auto whitespace-nowrap space-x-4 flex pb-4">
        <?php
        $berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_post DESC");
        $ada_prestasi = false;
        while ($b = mysqli_fetch_assoc($berita)) :
            if (
                !empty($b['gambar_utama']) &&
                (stripos($b['judul'], 'prestasi') !== false || stripos($b['isi'], 'prestasi') !== false)
            ):
                $ada_prestasi = true;
        ?>
        <div class="inline-block w-80 flex-shrink-0 bg-white rounded shadow-md border border-yellow-300">
            <a href="berita_detail.php?id=<?= $b['id'] ?>">
                <img src="upload/<?= $b['gambar_utama'] ?>" class="h-48 w-full object-cover rounded-t" alt="Gambar Prestasi">
            </a>
            <div class="p-4">
                <p class="text-sm text-gray-500 mb-1"><?= date('d M Y', strtotime($b['tanggal_post'])) ?> • <?= htmlspecialchars($b['penulis']) ?></p>
                <h3 class="text-md font-bold text-yellow-700 mb-1"><?= htmlspecialchars($b['judul']) ?></h3>
                <p class="text-sm text-justify text-gray-700"><?= nl2br(htmlspecialchars(substr($b['isi'], 0, 150))) ?>...</p>
                <a href="berita_detail.php?id=<?= $b['id'] ?>" class="text-sm text-yellow-600 hover:underline mt-2 inline-block">Baca Selengkapnya</a>
            </div>
        </div>
        <?php
            endif;
        endwhile;

        if (!$ada_prestasi) {
            echo "<p class='text-center text-gray-600'>Belum ada berita prestasi siswa.</p>";
        }
        ?>
    </div>
</section>

<!-- FOOTER -->
<footer class="text-center text-sm text-gray-500 py-4">
    &copy; <?= date('Y') ?> Pondok Pesantren Roudlotul Quran. All rights reserved.
</footer>

</body>
</html>
