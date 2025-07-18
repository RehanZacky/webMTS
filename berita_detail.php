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
    <title><?= htmlspecialchars($berita['judul']) ?> - Ponpes Roudlotul Quran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-gray-100 font-sans text-gray-800">

<header class="bg-white/75 backdrop-blur-lg sticky top-0 z-50 shadow-sm">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <a href="index.php" class="flex items-center gap-3">
            <img src="https://placehold.co/40x40/16a34a/white?text=A" alt="Logo" class="h-10 w-10">
            <span class="text-xl font-bold text-gray-800 leading-tight">Pondok Pesantren <br> Roudlotul Quran</span>
        </a>
        <nav class="hidden md:flex items-center space-x-8">
            <a href="index.php" class="text-gray-600 hover:text-green-600 font-semibold">Beranda</a>
            <a href="profil.php" class="text-gray-600 hover:text-green-600 font-semibold">Profil</a>
            <a href="berita.php" class="text-green-600 font-bold border-b-2 border-green-600 pb-1">Berita</a>
            <a href="galeri.php" class="text-gray-600 hover:text-green-600 font-semibold">Galeri</a>
        </nav>
        <div class="md:hidden">
            <button id="menu-toggle" class="focus:outline-none">
                <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
    <div id="mobile-menu" class="hidden md:hidden bg-white px-6 pb-4">
        <a href="index.php" class="block py-2 text-gray-700 hover:text-green-600">Beranda</a>
        <a href="profil.php" class="block py-2 text-gray-700 hover:text-green-600">Profil</a>
        <a href="berita.php" class="block py-2 text-green-600 font-semibold">Berita</a>
        <a href="galeri.php" class="block py-2 text-gray-700 hover:text-green-600">Galeri</a>
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
                    <img src="upload/<?= htmlspecialchars($berita['gambar_utama']) ?>" alt="Gambar: <?= htmlspecialchars($berita['judul']) ?>"
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
                                <img src="upload/<?= htmlspecialchars($lainnya['gambar_utama']) ?>" alt="<?= htmlspecialchars($lainnya['judul']) ?>" class="w-20 h-20 rounded-md object-cover flex-shrink-0">
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

<footer class="bg-gray-800 text-gray-300">
     <div class="container mx-auto px-6 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-2">
                <h4 class="text-white text-lg font-semibold mb-4">Pondok Pesantren Roudlotul Quran</h4>
                <p class="text-gray-400">Pondok Pesantren Roudlotul Quran berkomitmen untuk mendidik generasi muda Islami yang berakhlak mulia, berilmu pengetahuan, dan berjiwa pemimpin dalam membangun peradaban yang berkualitas.</p>
            </div>
            <div>
                <h4 class="text-white text-lg font-semibold mb-4">Kontak Kami</h4>
                <ul class="space-y-2 text-gray-400">
                    <li>Jl. Raya Pesantren No. 123, Sidoarjo, Jawa Timur 61234</li>
                    <li>+62 31 1234 5678</li>
                    <li>info@roudlotulquran.ponpes.id</li>
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
            <p>© <?= date('Y') ?> Pondok Pesantren Roudlotul Quran. All rights reserved.</p>
        </div>
    </div>
</footer>

</body>
</html>