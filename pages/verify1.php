<?php
include '../includes/config.php';

if (isset($_GET['code'])) {
    $code = $conn->real_escape_string($_GET['code']);
    
    $sql = "UPDATE users SET is_verified = 1 WHERE verification_code = '$code'";
    
    if ($conn->query($sql) === TRUE && $conn->affected_rows > 0) {
        $message = "Email verified successfully! You can now login.";
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <h3 class="card-title mb-4">Email Verification</h3>
                        <p><?php echo $message; ?></p>
                        <a href="login.php" class="btn btn-primary">Go to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>