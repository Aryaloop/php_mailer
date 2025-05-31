<?php
// includes/config.php

session_start([
    'cookie_httponly' => true,
    'use_strict_mode' => true
]);

// Database Configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'user_authentication';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// TMDB Configuration
define('TMDB_API_KEY', '3097f4aed12eb128588745df1a12a5f0');
define('TMDB_BASE_URL', 'https://api.themoviedb.org/3');
define('TMDB_IMAGE_URL', 'https://image.tmdb.org/t/p/w500');

// Load PHPMailer dengan cara yang benar
require_once __DIR__ . '/../vendor/autoload.php';

// Tambahkan ini untuk membantu IDE mengenali class
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Fungsi untuk mengirim email reset password
 */
function sendPasswordResetEmail($email, $reset_token) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'aryaabdulmughni@gmail.com';
        $mail->Password = 'yxaqmyiopxxhxymt';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('aryaabdulmughni18@gmail.com', 'Admin T Informatica');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body = "
            <h2>Password Reset Request</h2>
            <p>You requested to reset your password. Click the link below:</p>
            <a href='http://localhost/tugas-sqa-mail/pages/reset-password.php?token=$reset_token'>
                Reset Password
            </a>
            <p><small>Link expires in 1 hour.</small></p>
        ";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Email error: " . $e->getMessage());
        return false;
    }
}