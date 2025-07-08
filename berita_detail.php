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
    <title><?= htmlspecialchars($berita['judul']) ?> - MINUHA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<header class="bg-green-700 text-white py-6 text-center">
    <h1 class="text-3xl font-bold">Detail Berita</h1>
</header>

<main class="max-w-3xl mx-auto p-6 bg-white mt-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-2"><?= htmlspecialchars($berita['judul']) ?></h2>
    <p class="text-sm text-gray-500 mb-4"><?= date('d M Y', strtotime($berita['tanggal_post'])) ?> • <?= htmlspecialchars($berita['penulis']) ?></p>

    <?php if (!empty($berita['gambar_utama'])): ?>
        <img src="upload/<?= $berita['gambar_utama'] ?>" class="rounded mb-4 max-h-80 w-full object-cover" alt="Gambar Berita">
    <?php endif; ?>

    <p class="text-justify mb-4"><?= nl2br(htmlspecialchars($berita['isi'])) ?></p>

    <?php if (!empty($berita['video_youtube'])): ?>
        <?php
        parse_str(parse_url($berita['video_youtube'], PHP_URL_QUERY), $ytParams);
        $videoID = $ytParams['v'] ?? null;
        ?>
        <?php if ($videoID): ?>
            <div class="flex justify-center mt-6">
                <div class="w-full max-w-2xl aspect-video">
                    <iframe class="w-full h-full rounded" src="https://www.youtube.com/embed/<?= $videoID ?>" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <a href="index.php" class="inline-block mt-6 text-green-700 hover:underline">⬅️ Kembali ke Beranda</a>
</main>

</body>
</html>
