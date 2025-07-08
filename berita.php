<?php
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita - MINUHA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

<header class="bg-green-700 text-white py-6 text-center">
    <h1 class="text-3xl font-bold">Berita Sekolah</h1>
    <nav class="mt-2 space-x-4">
        <a href="index.php" class="hover:underline">Beranda</a>
        <a href="#" class="hover:underline">Profil</a>
        <a href="#" class="hover:underline">Kontak</a>
    </nav>
</header>

<main class="p-6 max-w-4xl mx-auto">
    <h2 class="text-2xl font-semibold mb-6">Daftar Berita</h2>

    <?php
    $berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_post DESC");
    while ($b = mysqli_fetch_assoc($berita)) :
    ?>
        <div class="bg-white shadow-md rounded p-6 mb-8">
            <h3 class="text-xl font-bold mb-1"><?= htmlspecialchars($b['judul']) ?></h3>
            <p class="text-sm text-gray-500 mb-3"><?= date('d M Y', strtotime($b['tanggal_post'])) ?> • <?= htmlspecialchars($b['penulis']) ?></p>

            <?php if (!empty($b['gambar_utama'])): ?>
                <img src="upload/<?= $b['gambar_utama'] ?>" class="mt-2 max-h-60 rounded shadow mx-auto mb-4" alt="Gambar Berita">
            <?php endif; ?>

            <p class="text-justify"><?= nl2br(htmlspecialchars(substr($b['isi'], 0, 300))) ?>...</p>

            <?php if (!empty($b['video_youtube'])): ?>
                <?php
                parse_str(parse_url($b['video_youtube'], PHP_URL_QUERY), $ytParams);
                $videoID = $ytParams['v'] ?? null;
                ?>
                <?php if ($videoID): ?>
                    <div class="flex justify-center mt-4">
                        <div class="w-full max-w-2xl aspect-video">
                            <iframe class="w-full h-full rounded" src="https://www.youtube.com/embed/<?= $videoID ?>" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</main>

<footer class="text-center text-sm text-gray-500 py-4">
    &copy; <?= date('Y') ?> MINUHA Sidoarjo. All rights reserved.
</footer>

</body>
</html>
