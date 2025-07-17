<?php
include 'auth.php';
include '../koneksi.php';

// Proses simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['isi'] as $jenis => $isi) {
        $jenis = mysqli_real_escape_string($koneksi, $jenis);
        $isi = mysqli_real_escape_string($koneksi, $isi);
        mysqli_query($koneksi, "UPDATE profil SET isi = '$isi' WHERE jenis = '$jenis'");
    }
    $success = "Profil sekolah berhasil diperbarui.";
}

// Ambil data
$data = [];
$query = mysqli_query($koneksi, "SELECT * FROM profil");
while ($row = mysqli_fetch_assoc($query)) {
    $data[$row['jenis']] = $row['isi'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ganti Video Profil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<nav class="bg-green-700 text-white px-6 py-4 flex justify-between items-center">
    <h1 class="text-xl font-bold">Ganti Video Profil</h1>
    <a href="dashboard_admin.php" class="bg-white text-green-700 px-3 py-1 rounded hover:bg-gray-100">⬅️ Dashboard</a>
</nav>

<main class="p-6 max-w-4xl mx-auto bg-white rounded shadow mt-6">
    <?php if (isset($success)) echo "<p class='text-green-600 mb-4'>$success</p>"; ?>
    <form method="POST">
        <?php
        $judul = [
            'sambutan_kepala' => 'Link Video Youtube'
        ];
        foreach ($judul as $jenis => $label) :
        ?>
        <div class="mb-6">
            <label class="block font-bold mb-2"><?= $label ?></label>
                <?php if ($jenis === 'sambutan_kepala'): ?>
                    <input type="url" name="isi[<?= $jenis ?>]" value="<?= htmlspecialchars($data[$jenis] ?? '') ?>" placeholder="Tempel link YouTube (contoh: https://www.youtube.com/watch?v=...)" class="w-full border p-2 rounded" required>
                <?php else: ?>
                    <textarea name="isi[<?= $jenis ?>]" rows="6" class="w-full border p-2 rounded"><?= htmlspecialchars($data[$jenis] ?? '') ?></textarea>
                <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Simpan Perubahan</button>
    </form>
</main>

</body>
</html>