<?php
include '../includes/config.php';

if (isset($_GET['token'])) {
    $token = $conn->real_escape_string($_GET['token']);
    
    // Check if token is valid and not expired
    $sql = "SELECT * FROM users WHERE reset_token = '$token' 
            AND reset_token_expiry > NOW()";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 0) {
        $error = "Invalid or expired reset token.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'reset_password') {
        $token = $conn->real_escape_string($_POST['token']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        
        // Update password and clear reset token
        $sql = "UPDATE users SET password = '$password', 
                reset_token = NULL, reset_token_expiry = NULL 
                WHERE reset_token = '$token'";
        
        if ($conn->query($sql) === TRUE) {
            $success = "Password has been reset successfully! You can now login.";
        } else {
            $error = "Error resetting password: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - T Informatica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Reset Password</h3>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                            <div class="text-center">
                                <a href="forgot-password.php" class="text-decoration-none">Request new reset link</a>
                            </div>
                        <?php elseif (isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                            <div class="text-center">
                                <a href="login.php" class="btn btn-primary">Login Now</a>
                            </div>
                        <?php else: ?>
                            <form action="reset-password.php" method="POST">
                                <input type="hidden" name="action" value="reset_password">
                                <input type="hidden" name="token" value="<?php echo $token; ?>">
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Client-side password validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm_password = document.getElementById('confirm_password').value;
            
            if (password !== confirm_password) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long!');
            }
        });
    </script>
</body>
</html>