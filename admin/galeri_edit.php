<?php
include 'auth.php';
include '../koneksi.php';

// Hapus foto
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $cek = mysqli_query($koneksi, "SELECT nama_file FROM galeri WHERE id = $id");
    $row = mysqli_fetch_assoc($cek);
    if ($row && file_exists("../upload/" . $row['nama_file'])) {
        unlink("../upload/" . $row['nama_file']);
    }
    mysqli_query($koneksi, "DELETE FROM galeri WHERE id = $id");
    header("Location: galeri_edit.php");
    exit;
}

// Tambah foto
if (isset($_POST['tambah'])) {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $tanggal = date("Y-m-d");

    $nama_file = "";
    if ($_FILES['foto']['name']) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nama_file = "galeri_" . time() . "." . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], "../upload/$nama_file");
    }

    mysqli_query($koneksi, "INSERT INTO galeri (judul, deskripsi, nama_file, tanggal_upload)
        VALUES ('$judul', '$deskripsi', '$nama_file', '$tanggal')");
    header("Location: galeri_edit.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Galeri Foto</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<nav class="bg-blue-700 text-white px-6 py-4 flex justify-between items-center">
    <h1 class="text-xl font-bold">Manajemen Galeri Foto</h1>
    <a href="dashboard.php" class="bg-white text-blue-700 px-3 py-1 rounded hover:bg-gray-100">⬅️ Dashboard</a>
</nav>

<main class="p-6 max-w-5xl mx-auto">
    <h2 class="text-2xl font-semibold mb-4">Tambah Foto</h2>
    <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow mb-6">
        <div class="mb-4">
            <label class="font-bold block">Judul Foto</label>
            <input type="text" name="judul" required class="w-full border p-2 rounded">
        </div>
        <div class="mb-4">
            <label class="font-bold block">Deskripsi</label>
            <textarea name="deskripsi" rows="4" class="w-full border p-2 rounded"></textarea>
        </div>
        <div class="mb-4">
            <label class="font-bold block">File Foto</label>
            <input type="file" name="foto" accept="image/*" required class="border p-2 rounded">
        </div>
        <button type="submit" name="tambah" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tambah Foto</button>
    </form>

    <h2 class="text-2xl font-semibold mb-4">Daftar Galeri</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <?php
        $galeri = mysqli_query($koneksi, "SELECT * FROM galeri ORDER BY tanggal_upload DESC");
        while ($g = mysqli_fetch_assoc($galeri)) :
        ?>
            <div class="bg-white p-4 rounded shadow">
                <img src="../upload/<?= $g['nama_file'] ?>" class="rounded mb-2 w-full aspect-video object-cover" alt="Foto Galeri">
                <h3 class="font-bold text-lg"><?= htmlspecialchars($g['judul']) ?></h3>
                <p class="text-sm text-gray-600 mb-2"><?= nl2br(htmlspecialchars($g['deskripsi'])) ?></p>
                <small class="text-gray-500"><?= date('d M Y', strtotime($g['tanggal_upload'])) ?></small>
                <div class="mt-2">
                    <a href="galeri_edit.php?hapus=<?= $g['id'] ?>" onclick="return confirm('Yakin hapus foto ini?')" class="text-red-600 hover:underline">🗑️ Hapus</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</main>

</body>
</html>
