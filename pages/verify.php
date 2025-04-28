<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// session_start(); // Tambahkan ini untuk mulai session
include '../includes/config.php';

if (isset($_GET['code'])) {
    $code = $conn->real_escape_string($_GET['code']);

    // Cari user berdasarkan verification_code
    $sql = "SELECT * FROM users WHERE verification_code = '$code' AND is_verified = 0";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        // Update status verified
        $conn->query("UPDATE users SET is_verified = 1 WHERE verification_code = '$code'");

        // Ambil data user
        $user = $result->fetch_assoc();

        // Set session login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];

        // Redirect langsung ke dashboard (atau halaman utama kamu)
        header("Location: http://localhost/tugas-sqa-mail/pages/dashboard.php");
        exit();
    } else {
        $message = "Invalid verification code or email already verified.";
    }
} elseif (isset($_GET['email'])) {
    $email = $conn->real_escape_string($_GET['email']);
    $message = "A verification link has been sent to $email. Please check your email.";
} else {
    header("Location: login.php");
    exit();
}
