<?php include '../includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - T Informatica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-2">LOGIN</h3>
                        <p class="text-center text-muted mb-4">Software Quality - 2025</p>

                        <form id="loginForm" action="../includes/auth.php" method="POST">
                            <input type="hidden" name="action" value="login">

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="g-recaptcha mb-3" data-sitekey="6Ld0BVErAAAAAGcUTY7yR_Ox5DbPkmy1bEYRHS7W"></div>

                            <button type="submit" class="btn btn-primary w-100">Login</button>

                            <!-- Notifikasi tampil di bawah tombol submit -->
                            <div id="alert-container" class="mt-3">
                                <?php
                                if (isset($_GET['error'])) {
                                    if ($_GET['error'] === 'captcha_failed') {
                                        echo '<div class="alert alert-danger text-center">Captcha verification failed. Please try again.</div>';
                                    } elseif ($_GET['error'] === 'empty_captcha') {
                                        echo '<div class="alert alert-warning text-center">Please complete the captcha.</div>';
                                    } elseif ($_GET['error'] === 'invalid_credentials') {
                                        echo '<div class="alert alert-danger text-center">Username or password incorrect.</div>';
                                    } elseif ($_GET['error'] === 'not_verified') {
                                        echo '<div class="alert alert-info text-center">Please verify your email before logging in.</div>';
                                    }
                                }
                                ?>
                            </div>

                            <div class="text-center mt-3">
                                <a href="register.php" class="text-decoration-none d-block">Register</a>
                                <a href="forgot-password.php" class="text-decoration-none d-block">Forgot Password?</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Validasi reCAPTCHA di sisi klien -->
    <script>
    document.getElementById('loginForm').addEventListener('submit', function (e) {
        var captchaResponse = grecaptcha.getResponse();

        if (captchaResponse.length === 0) {
            e.preventDefault();

            const alertContainer = document.getElementById('alert-container');
            alertContainer.innerHTML = `
                <div class="alert alert-warning text-center">Please complete the captcha before submitting.</div>
            `;
        }
    });
    </script>
</body>
</html>
