<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start(); // Pastikan session dimulai
include '../includes/config.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Cek apakah kode ini ada di database
    $check = $conn->query("SELECT * FROM users WHERE verification_code = '$code'");

    if ($check->num_rows > 0) {
        // Jika ada, update is_verified
        $update = $conn->query("UPDATE users SET is_verified = 1, verification_code = NULL WHERE verification_code = '$code'");

        if ($update) {
            // Set session dan pesan sukses
            $user = $check->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_verified'] = 1;
            $_SESSION['success_message'] = "Verifikasi berhasil, silakan login.";

            header("Location: login.php");
            exit();
        } else {
            echo "Gagal update status verifikasi!";
        }
    } else {
        echo "Kode verifikasi tidak valid atau sudah digunakan.";
    }
} else {
    echo "Tidak ada kode verifikasi.";
}
?>
