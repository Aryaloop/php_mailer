<?php
require 'config.php';
require '../vendor/autoload.php'; // Pastikan sudah install PHPMailer pakai composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function registerUser($conn, $name, $email, $address, $phone, $username, $password)
{
    $conn->begin_transaction();
    try {
        // Cek duplikat email atau username
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement gagal: " . $conn->error);
        }
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            throw new Exception("Email atau username sudah digunakan.");
        }
        $stmt->free_result();
        $stmt->close();

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Generate kode verifikasi
        $verificationCode = bin2hex(random_bytes(16));

        // Insert user baru
        $stmt = $conn->prepare("INSERT INTO users (name, email, address, phone, username, password, verification_code) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $email, $address, $phone, $username, $hashedPassword, $verificationCode);
        $stmt->execute();
        $stmt->close();

        // Kirim email verifikasi
        sendVerificationEmail($email, $verificationCode);

        $conn->commit();
        return "Registrasi berhasil. Silakan cek email untuk verifikasi.";
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}

function sendVerificationEmail($toEmail, $verificationCode)
{
    $mail = new PHPMailer(true);
    try {
        // Konfigurasi SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP server Gmail
        $mail->SMTPAuth = true;
        $mail->Username = 'aryaabdulmughni18@gmail.com'; // Ganti dengan email kamu
        $mail->Password = 'yxaqmyiopxxhxymt'; // Ganti dengan password/aplikasi key kamu
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('aryaabdulmughni18@gmail.com', 'Login');
        $mail->addAddress($toEmail);
        $mail->Subject = 'Verifikasi Email Anda';
        $mail->Body    = "Klik link berikut untuk verifikasi akun Anda: http://localhost/tugas-sqa-mail/pages/verify.php?code=$verificationCode";

        $mail->send();
    } catch (Exception $e) {
        throw new Exception("Email tidak dapat dikirim. Mailer Error: {$mail->ErrorInfo}");
    }
}
function loginUser($conn, $username, $password)
{
    // session_start(); // <- Penting untuk mulai session

    $stmt = $conn->prepare("SELECT id, password, is_verified FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Username tidak ditemukan.");
    }

    $user = $result->fetch_assoc();

    // Cek password
    if (!password_verify($password, $user['password'])) {
        throw new Exception("Password salah.");
    }

    // Cek apakah sudah verifikasi email
    if ($user['is_verified'] != 1) {
        throw new Exception("Email belum diverifikasi. Silakan cek email Anda.");
    }

    // Simpan data login ke session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $username;

    // Redirect ke dashboard
    header("Location: ../pages/dashboard.php");
    exit;
}

// Handler register di sini (misal POST dari form)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'register') {
    try {
        $message = registerUser($conn, $_POST['name'], $_POST['email'], $_POST['address'], $_POST['phone'], $_POST['username'], $_POST['password']);
        echo $message;
    } catch (Exception $e) {
        echo "Gagal: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'login') {
    try {
        loginUser($conn, $_POST['username'], $_POST['password']);
    } catch (Exception $e) {
        echo "Gagal: " . $e->getMessage();
    }
}
