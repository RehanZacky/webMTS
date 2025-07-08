<?php
session_start();
include '../../../koneksi.php'; // sesuaikan path jika di luar folder

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' LIMIT 1");
    $user = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: ../../../admin/dashboard_admin.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow w-full max-w-md">
        <h2 class="text-xl font-bold mb-4 text-center">Login Admin</h2>
        <?php if (isset($error)) echo "<p class='text-red-500 mb-2'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required class="w-full border px-3 py-2 mb-3 rounded" />
            <input type="password" name="password" placeholder="Password" required class="w-full border px-3 py-2 mb-3 rounded" />
            <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Login</button>
        </form>
    </div>
</body>
</html>
