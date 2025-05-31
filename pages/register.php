<?php include '../includes/config.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - T Informatica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Register</h3>

                        <form action="../includes/auth.php" method="POST">
                            <input type="hidden" name="action" value="register">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" pattern="^\d{9,12}$" title="Masukkan 9 hingga 13 digit angka" required>
                                <?php
                                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['phone'])) {
                                    $phone = $_POST['phone'];

                                    if (!preg_match('/^\d{9,13}$/', $phone)) {
                                        echo '<div class="text-danger mt-2">Nomor telepon harus terdiri dari 9 hingga 12 digit angka!</div>';
                                    }
                                }
                                ?>

                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        title="Password must be at least 8 characters long, include an uppercase letter, a number, and a special character"
                                        required>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="login.php" class="btn btn-secondary me-md-2">Cancel</a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">Create Account</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const form = document.querySelector('form');
        const passwordInput = document.getElementById('password');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', function(e) {
            const password = passwordInput.value.trim();

            // Cek minimal panjang
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long!');
                return;
            }

            // Cek kekuatan password (minimal 1 huruf besar, 1 angka, 1 simbol)
            const strongPasswordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/;
            if (!strongPasswordRegex.test(password)) {
                e.preventDefault();
                alert('Password must contain at least one uppercase letter, one number, and one special character.');
                return;
            }

            // Disable tombol submit
            submitBtn.disabled = true;
            submitBtn.innerText = 'Please wait...';
        });
    </script>

</body>

</html>