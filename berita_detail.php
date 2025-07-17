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

<!-- NAVBAR UTAMA -->
<header class="bg-white/75 backdrop-blur-lg sticky top-0 z-50 shadow-sm">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <a href="index.php" class="flex items-center gap-3">
            <img src="https://placehold.co/40x40/16a34a/white?text=A" alt="Logo" class="h-10 w-10">
            <span class="text-xl font-bold text-gray-800 leading-tight">Pondok Pesantren <br> Roudlotul Quran</span>
        </a>
        <!-- Desktop Menu -->
        <nav class="hidden md:flex items-center space-x-8">
            <a href="index.php" class="text-gray-600 hover:text-green-600 font-semibold">Beranda</a>
            <a href="profil.php" class="text-gray-600 hover:text-green-600 font-semibold">Profil</a>
            <a href="berita.php" class="text-gray-600 hover:text-green-600 font-semibold">Berita</a>
            <a href="galeri.php" class="text-gray-600 hover:text-green-600 font-semibold">Galeri</a>
        </nav>

        <!-- Mobile Hamburger Menu Button -->
        <div class="md:hidden">
            <button id="menu-toggle" class="focus:outline-none">
                <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-white px-6 pb-4">
        <a href="index.php" class="block py-2 text-gray-700 hover:text-green-600">Beranda</a>
        <a href="profil.php" class="block py-2 text-gray-700 hover:text-green-600">Profil</a>
        <a href="berita.php" class="block py-2 text-gray-700 hover:text-green-600">Berita</a>
        <a href="galeri.php" class="block py-2 text-gray-700 hover:text-green-600">Galeri</a>
    </div>
</header>

<!-- SCRIPT UNTUK MOBILE MENU -->
<script>
    document.getElementById("menu-toggle").addEventListener("click", function () {
        const menu = document.getElementById("mobile-menu");
        menu.classList.toggle("hidden");
    });
</script>


<!-- HEADER -->
<header class="bg-gradient-to-b from-green-700 to-green-400 text-white text-center py-20">
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
