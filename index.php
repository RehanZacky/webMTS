<?php
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Beranda - MINUHA</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<header class="bg-green-700 text-white py-6 text-center">
<h1 class="text-3xl font-bold">MINUHA Sidoarjo</h1>
<nav class="mt-2 space-x-4">
    <a href="#" class="hover:underline">Beranda</a>
    <a href="#" class="hover:underline">Profil</a>
    <a href="#" class="hover:underline">Guru</a>
    <a href="#" class="hover:underline">Pendaftaran</a>
    <a href="berita.php" class="hover:underline">Berita</a>
    <a href="#" class="hover:underline">Kontak</a>
</nav>
</header>

<section class="bg-green-100 text-center py-10">
<h2 class="text-2xl font-semibold">Selamat Datang di Website Resmi Sekolah</h2>
<p class="mt-2">Mewujudkan generasi Islami dan berprestasi</p>
</section>


<?php
$visi = mysqli_query($koneksi, "SELECT isi FROM profil WHERE jenis='visi'");
$misi = mysqli_query($koneksi, "SELECT isi FROM profil WHERE jenis='misi'");
$isi_visi = mysqli_fetch_assoc($visi)['isi'] ?? '';
$isi_misi = mysqli_fetch_assoc($misi)['isi'] ?? '';
?>

<section id="visi-misi" class="py-10 bg-white text-center">
  <h2 class="text-2xl font-bold mb-4">Visi & Misi</h2>
  <div class="max-w-3xl mx-auto text-left px-4">
    <h3 class="text-lg font-semibold mb-2">Visi:</h3>
    <p><?= nl2br($isi_visi) ?></p>

    <h3 class="text-lg font-semibold mt-6 mb-2">Misi:</h3>
    <p><?= nl2br($isi_misi) ?></p>
  </div>
</section>

<?php
$sejarah = mysqli_query($koneksi, "SELECT isi FROM profil WHERE jenis='sejarah'");
$isi_sejarah = mysqli_fetch_assoc($sejarah)['isi'] ?? '';
?>

<section id="sejarah" class="py-10 bg-gray-50 text-center">
  <h2 class="text-2xl font-bold mb-4">Sejarah Singkat Sekolah</h2>
  <div class="max-w-3xl mx-auto text-left px-4">
    <p><?= nl2br($isi_sejarah) ?></p>
  </div>
</section>

<?php
$sambutan = mysqli_query($koneksi, "SELECT isi FROM profil WHERE jenis='sambutan_kepala'");
$isi_sambutan = mysqli_fetch_assoc($sambutan)['isi'] ?? '';
?>

<section class="section py-10 bg-white text-center" id="profil">
    <h3 class="text-2xl font-bold mb-4">Sambutan Kepala Sekolah</h3>
    <?php
    $profil = mysqli_query($koneksi, "SELECT isi FROM profil WHERE jenis = 'sambutan_kepala' LIMIT 1");
    $data_profil = mysqli_fetch_assoc($profil);
    $link = $data_profil['isi'] ?? '';

    // Ekstrak ID dari link YouTube
    parse_str(parse_url($link, PHP_URL_QUERY), $ytParams);
    $videoID = $ytParams['v'] ?? null;

    if ($videoID) {
        echo '<div class="flex justify-center">';
        echo '<div class="w-full max-w-2xl aspect-video">';
        echo "<iframe class='w-full h-full' src='https://www.youtube.com/embed/$videoID' frameborder='0' allowfullscreen></iframe>";
        echo '</div></div>';
    } else {
        echo "<p class='text-gray-500'>Link video tidak valid atau belum diisi.</p>";
    }
    ?>
</section>



<section class="py-8 bg-gray-100" id="berita">
    <div class="max-w-5xl mx-auto px-4">
        <h3 class="text-2xl font-bold mb-4 text-center">Berita Terbaru</h3>
        <div class="grid md:grid-cols-3 gap-6">
            <?php
            $berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_post DESC LIMIT 3");
            while ($b = mysqli_fetch_assoc($berita)) :
            ?>
                <div class="bg-white rounded shadow p-4">
                    <?php if (!empty($b['gambar_utama'])): ?>
                        <img src="upload/<?= $b['gambar_utama'] ?>" class="mb-3 h-40 w-full object-cover rounded" alt="Gambar Berita">
                    <?php endif; ?>
                    <h4 class="text-lg font-semibold mb-2"><?= htmlspecialchars($b['judul']) ?></h4>
                    <small class="text-gray-500"><?= date('d M Y', strtotime($b['tanggal_post'])) ?></small>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>


<section class="section" id="statistik">
    <h3>Info Sekolah</h3>
    <?php
    $data_statistik = mysqli_query($koneksi, "SELECT * FROM info_statistik ORDER BY id ASC");
    $stat = mysqli_query($koneksi, "SELECT * FROM info_statistik");
    while ($s = mysqli_fetch_assoc($stat)) {
    }
    ?>
    <h2 class="text-2xl font-bold mb-4">Statistik Sekolah</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
        <?php while ($row = mysqli_fetch_assoc($data_statistik)) : ?>
            <div class="bg-white shadow rounded p-4">
                <h3 class="text-lg font-semibold"><?= $row['label'] ?></h3>
                <p class="text-3xl text-green-600 font-bold"><?= $row['nilai'] ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<footer>
    <p>&copy; <?= date('Y') ?> MINUHA Sidoarjo. All rights reserved.</p>
</footer>

</body>
</html>
