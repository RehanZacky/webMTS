<?php
include 'koneksi.php';

if (!isset($_GET['id'])) {
    echo "Berita tidak ditemukan.";
    exit;
}

$id = intval($_GET['id']);
$data = mysqli_query($koneksi, "SELECT * FROM berita WHERE id = $id");
$berita = mysqli_fetch_assoc($data);

if (!$berita) {
    echo "Berita tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($berita['judul']) ?> - Ponpes Roudlotul Quran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-gray-100 font-sans text-gray-800">

<!-- NAVBAR -->
<nav class="bg-white shadow-md fixed w-full z-50 top-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex-shrink-0 flex items-center space-x-2">
                <img class="h-10 w-auto" src="logo.png" alt="Logo">
                <span class="font-bold text-green-700">Ponpes Roudlotul Quran</span>
                
            </div>
            <div class="hidden md:flex space-x-6 items-center">
                <a href="index.php" class="hover:text-green-700">Beranda</a>
                <a href="#profil.php" class="hover:text-green-700">Profil</a>
                <a href="berita.php" class="hover:text-green-700">Berita</a>
                <a href="galeri.php" class="hover:text-green-700">Galeri</a>
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
            <a href="#" class="block px-4">Profil</a>
            <a href="berita.php" class="block px-4">Berita</a>
            <a href="#" class="block px-4">Galeri</a>
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
<header class="mt-16 bg-gradient-to-b from-green-700 to-green-500 text-white text-center py-12">
    <h1 class="text-3xl md:text-4xl font-bold px-4">Detail Berita</h1>
</header>

<!-- KONTEN BERITA -->
<main class="max-w-4xl mx-auto p-6 bg-white mt-6 rounded shadow-lg mb-12">
    <h2 class="text-2xl md:text-3xl font-bold text-green-700 mb-3"><?= htmlspecialchars($berita['judul']) ?></h2>
    <p class="text-sm text-gray-500 mb-6">
        <?= date('d M Y', strtotime($berita['tanggal_post'])) ?> • <?= htmlspecialchars($berita['penulis']) ?>
    </p>

    <?php if (!empty($berita['gambar_utama'])): ?>
        <div class="mb-6">
            <img src="upload/<?= $berita['gambar_utama'] ?>" class="rounded-lg w-full max-h-[600px] object-cover shadow" alt="Gambar Berita">
        </div>
    <?php endif; ?>

    <article class="text-justify text-[17px] leading-relaxed">
        <?= nl2br(htmlspecialchars($berita['isi'])) ?>
    </article>

    <?php if (!empty($berita['video_youtube'])): ?>
        <?php
        parse_str(parse_url($berita['video_youtube'], PHP_URL_QUERY), $ytParams);
        $videoID = $ytParams['v'] ?? null;
        ?>
        <?php if ($videoID): ?>
            <div class="mt-10 flex justify-center">
                <div class="w-full max-w-3xl aspect-video">
                    <iframe class="w-full h-full rounded" src="https://www.youtube.com/embed/<?= $videoID ?>" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="mt-10">
        <a href="berita.php" class="inline-flex items-center text-green-700 hover:underline text-sm">
            ⬅️ Kembali ke Daftar Berita
        </a>
    </div>
</main>

<!-- FOOTER -->
<footer class="text-center text-sm text-gray-500 py-6">
    &copy; <?= date('Y') ?> Pondok Pesantren Roudlotul Quran. All rights reserved.
</footer>

</body>
</html>
