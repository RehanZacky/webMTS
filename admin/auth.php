<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../secure/user/admin/admin_mts.php");
    exit;
}
?>