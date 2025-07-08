<?php
include 'auth.php';
include '../koneksi.php';

// Ambil jumlah berita
$berita = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM berita");
$jumlah_berita = mysqli_fetch_assoc($berita)['total'];

// Ambil jumlah data statistik (siswa, guru, staff)
$statistik = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM info_statistik");
$jumlah_statistik = mysqli_fetch_assoc($statistik)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

  <!-- Navbar -->
  <nav class="bg-green-700 text-white px-6 py-4 flex justify-between items-center">
    <h1 class="text-xl font-bold">Dashboard Admin</h1>
    <div>
      <span class="mr-4">Halo, <?= $_SESSION['username'] ?></span>
      <a href="../logout.php" class="bg-red-500 px-3 py-1 rounded hover:bg-red-600">Logout</a>
    </div>
  </nav>

  <!-- Konten -->
  <main class="p-6">
    <h2 class="text-2xl font-semibold mb-6">Ringkasan Website</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white rounded shadow p-6 text-center">
        <h3 class="text-lg font-bold mb-2">Total Berita</h3>
        <p class="text-4xl text-green-700"><?= $jumlah_berita ?></p>
        <a href="berita_edit.php" class="text-blue-600 hover:underline mt-2 block">Kelola Berita</a>
      </div>

      <div class="bg-white rounded shadow p-6 text-center">
        <h3 class="text-lg font-bold mb-2">Statistik Sekolah</h3>
        <p class="text-4xl text-green-700"><?= $jumlah_statistik ?></p>
        <a href="statistik_edit.php" class="text-blue-600 hover:underline mt-2 block">Kelola Statistik</a>
      </div>

      <div class="bg-white rounded shadow p-6 text-center">
        <h3 class="text-lg font-bold mb-2">Profil Sekolah</h3>
        <p class="text-4xl text-green-700">📝</p>
        <a href="profil_edit.php" class="text-blue-600 hover:underline mt-2 block">Edit Profil</a>
      </div>
    </div>
  </main>

</body>
</html>
