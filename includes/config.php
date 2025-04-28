<?php
session_start();

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'user_authentication';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Email configuration (using PHPMailer)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function sendPasswordResetEmail($email, $reset_token) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'aryaabdulmughni@gmail.com';
        $mail->Password   = 'yxaqmyiopxxhxymt';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('aryaabdulmughni18@gmail.com', 'Admin T Informatica');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = "<h2>Password Reset Request</h2>
    <p>You requested to reset your password. Click the link below:</p>
    <a href='http://localhost/tugas-sqa-mail/pages/reset-password.php?token=$reset_token' 
       style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>
       Reset Password
    </a>
    <p><small>Link expires in 1 hour.</small></p>
    <p>If you didn't request this, please ignore this email.</p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
// function sendVerificationEmail($email, $verification_code) {
//     require '../vendor/autoload.php'; // Jika pakai PHPMailer
    
//     $mail = new PHPMailer(true);
//     try {
//         // SMTP Configuration
//         $mail->isSMTP();
//         $mail->Host       = 'smtp.gmail.com';
//         $mail->SMTPAuth   = true;
//         $mail->Username   = 'aryaabdulmughni18@gmail.com'; // Ganti dengan email Anda
//         $mail->Password   = 'nfqc uxpb dpjt jnoa';    // Gunakan App Password
//         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//         $mail->Port       = 587;

//         // Email Content
//         $mail->setFrom('aryaabdulmughni18@gmail.com', 'Your App Name');
//         $mail->addAddress($email);
//         $mail->isHTML(true);
//         $mail->Subject = 'Email Verification';
//         $mail->Body    = "Klik link berikut untuk verifikasi: 
//                          <a href='http://localhost/tugas-sqa-mail/pages/verify.php?code=$verification_code'>
//                          Verify Email</a>";

//         $mail->send();
//         return true;
//     } catch (Exception $e) {
//         error_log("Email error: " . $mail->ErrorInfo);
//         return false;
//     }
// }