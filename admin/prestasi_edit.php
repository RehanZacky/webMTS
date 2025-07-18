<?php
include '../koneksi.php';

// Proses tambah prestasi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $tahun = $_POST['tahun'];

    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $path = "../upload/" . $gambar;

    if (move_uploaded_file($tmp, $path)) {
        mysqli_query($koneksi, "INSERT INTO prestasi (judul, deskripsi, tahun, gambar) VALUES ('$judul', '$deskripsi', '$tahun', '$gambar')");
    }
}

// Proses hapus prestasi
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $get_gambar = mysqli_query($koneksi, "SELECT gambar FROM prestasi WHERE id = $id");
    $row = mysqli_fetch_assoc($get_gambar);
    if ($row && file_exists("../upload/" . $row['gambar'])) {
        unlink("../upload/" . $row['gambar']);
    }
    mysqli_query($koneksi, "DELETE FROM prestasi WHERE id = $id");
    header("Location: prestasi.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Prestasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow-lg">
        <h1 class="text-2xl font-bold mb-4">Tambah Prestasi</h1>
        <form action="" method="post" enctype="multipart/form-data" class="space-y-4">
            <input type="text" name="judul" placeholder="Judul Prestasi" required class="w-full border rounded p-2" />
            <textarea name="deskripsi" placeholder="Deskripsi Prestasi" required class="w-full border rounded p-2"></textarea>
            <input type="text" name="tahun" placeholder="Tahun" required class="w-full border rounded p-2" />
            <input type="file" name="gambar" required class="w-full border rounded p-2" />
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Tambah</button>
        </form>
    </div>

    <div class="max-w-6xl mx-auto mt-10">
        <h2 class="text-xl font-bold mb-4">Daftar Prestasi</h2>
        <div class="grid md:grid-cols-3 gap-6">
            <?php
            $query = mysqli_query($koneksi, "SELECT * FROM prestasi ORDER BY id DESC");
            while ($data = mysqli_fetch_assoc($query)) {
            ?>
            <div class="bg-white rounded-xl shadow-md p-4">
                <img src="../upload/<?= htmlspecialchars($data['gambar']) ?>" alt="<?= htmlspecialchars($data['judul']) ?>" class="w-full h-48 object-cover rounded mb-3">
                <h3 class="text-lg font-semibold"><?= htmlspecialchars($data['judul']) ?></h3>
                <p class="text-gray-600 text-sm"><?= htmlspecialchars($data['tahun']) ?></p>
                <p class="text-sm mt-1"><?= nl2br(htmlspecialchars($data['deskripsi'])) ?></p>
                <a href="?hapus=<?= $data['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')" class="inline-block mt-3 text-red-600 hover:underline">Hapus</a>
            </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>
