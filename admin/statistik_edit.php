<?php
include 'auth.php';
include '../koneksi.php';

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['nilai'] as $id => $nilai) {
        $id = intval($id);
        $nilai = htmlspecialchars($nilai); // cegah XSS
        mysqli_query($koneksi, "UPDATE info_statistik SET nilai = '$nilai' WHERE id = $id");
    }
    $success = "Data statistik berhasil diperbarui.";
}

// Ambil data
$data = mysqli_query($koneksi, "SELECT * FROM info_statistik ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Statistik Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

  <nav class="bg-green-700 text-white px-6 py-4 flex justify-between items-center">
      <h1 class="text-xl font-bold">Statistik Sekolah</h1>
      <a href="dashboard_admin.php" class="bg-white text-green-700 px-3 py-1 rounded hover:bg-gray-100">⬅️ Dashboard</a>
  </nav>

  <main class="p-6 max-w-xl mx-auto bg-white rounded shadow mt-6">
    <h2 class="text-2xl font-semibold mb-4">Edit Data Statistik</h2>

    <?php if (isset($success)) echo "<p class='text-green-600 mb-4'>$success</p>"; ?>

    <form method="POST">
      <?php while ($row = mysqli_fetch_assoc($data)) : ?>
        <div class="mb-4">
          <label class="block font-semibold mb-1"><?= $row['label'] ?></label>
          <input type="text" name="nilai[<?= $row['id'] ?>]" value="<?= htmlspecialchars($row['nilai']) ?>" class="w-full border p-2 rounded" required>
        </div>
      <?php endwhile; ?>
      <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan Perubahan</button>
    </form>
  </main>

  
</body>
</html>
