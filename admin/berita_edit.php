<?php
include 'auth.php';
include '../koneksi.php';

// Hapus berita
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $cek = mysqli_query($koneksi, "SELECT gambar_utama FROM berita WHERE id = $id");
    $row = mysqli_fetch_assoc($cek);
    if ($row && file_exists("../upload/" . $row['gambar_utama'])) {
        unlink("../upload/" . $row['gambar_utama']);
    }
    mysqli_query($koneksi, "DELETE FROM berita WHERE id = $id");
    header("Location: berita_edit.php");
    exit;
}

// Tambah berita
if (isset($_POST['tambah'])) {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $isi = mysqli_real_escape_string($koneksi, $_POST['isi']);
    $penulis = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $video_youtube = mysqli_real_escape_string($koneksi, $_POST['video_youtube']);
    $tanggal = date("Y-m-d");

    $gambar_utama = "";
    if ($_FILES['gambar']['name']) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar_utama = "berita_" . time() . "." . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../upload/$gambar_utama");
    }

    mysqli_query($koneksi, "INSERT INTO berita (judul, isi, penulis, tanggal_post, gambar_utama, video_youtube)
        VALUES ('$judul', '$isi', '$penulis', '$tanggal', '$gambar_utama', '$video_youtube')");
    header("Location: berita_edit.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Berita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<nav class="bg-green-700 text-white px-6 py-4 flex justify-between items-center">
    <h1 class="text-xl font-bold">Manajemen Berita</h1>
    <a href="dashboard.php" class="bg-white text-green-700 px-3 py-1 rounded hover:bg-gray-100">⬅️ Dashboard</a>
</nav>

<main class="p-6 max-w-5xl mx-auto">
    <h2 class="text-2xl font-semibold mb-4">Tambah Berita</h2>
    <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow mb-6">
        <div class="mb-4">
            <label class="font-bold block">Judul Berita</label>
            <input type="text" name="judul" required class="w-full border p-2 rounded">
        </div>
        <div class="mb-4">
            <label class="font-bold block">Isi Berita</label>
            <textarea name="isi" rows="6" required class="w-full border p-2 rounded"></textarea>
        </div>
        <div class="mb-4">
            <label class="font-bold block">Penulis</label>
            <input type="text" name="penulis" required class="w-full border p-2 rounded">
        </div>
        <div class="mb-4">
            <label class="font-bold block">Gambar Utama (opsional)</label>
            <input type="file" name="gambar" accept="image/*" class="border p-2 rounded">
        </div>
        <div class="mb-4">
            <label class="font-bold block">Link Video YouTube (opsional)</label>
            <input type="url" name="video_youtube" placeholder="https://www.youtube.com/watch?v=..." class="w-full border p-2 rounded">
        </div>
        <button type="submit" name="tambah" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Tambah Berita</button>
    </form>

    <h2 class="text-2xl font-semibold mb-4">Daftar Berita</h2>
    <div class="space-y-4">
        <?php
        $berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_post DESC");
        while ($b = mysqli_fetch_assoc($berita)) :
        ?>
            <div class="bg-white p-4 rounded shadow">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold"><?= htmlspecialchars($b['judul']) ?></h3>
                        <small class="text-gray-500"><?= date('d M Y', strtotime($b['tanggal_post'])) ?> • <?= htmlspecialchars($b['penulis']) ?></small>
                    </div>
                    <a href="berita_edit.php?hapus=<?= $b['id'] ?>" onclick="return confirm('Yakin hapus berita ini?')" class="text-red-600 hover:underline">🗑️ Hapus</a>
                </div>

                <?php if (!empty($b['video_youtube'])) : ?>
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
                <?php elseif (!empty($b['gambar_utama'])) : ?>
                    <img src="../upload/<?= $b['gambar_utama'] ?>" class="mt-4 max-h-60 rounded shadow" alt="Gambar Berita">
                <?php endif; ?>

                <p class="mt-4"><?= nl2br(htmlspecialchars(substr($b['isi'], 0, 150))) ?>...</p>
            </div>
        <?php endwhile; ?>
    </div>
</main>

</body>
</html>
