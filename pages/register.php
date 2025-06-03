<?php
include '../includes/config.php'; // koneksi DB jika dibutuhkan

$errors = [];
$success = "";

// Proses form jika dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'register') {
    // Ambil dan sanitasi input
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validasi BVA
    if (strlen($name) < 1 || strlen($name) > 25 || !preg_match('/^[a-zA-Z\s]+$/', $name)) {
        $errors[] = "Nama lengkap harus terdiri dari 1–25 karakter dan hanya mengandung huruf dan spasi.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }

    if (strlen($address) < 5 || strlen($address) > 100) {
        $errors[] = "Alamat harus terdiri dari 5–100 karakter.";
    }

    if (!preg_match('/^\d{9,12}$/', $phone)) {
        $errors[] = "Nomor telepon harus terdiri dari 9–12 digit angka.";
    }

    if (strlen($username) < 4 || strlen($username) > 20 || !preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = "Username harus 4–20 karakter dan hanya berisi huruf, angka, dan underscore.";
    }

    if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $errors[] = "Password harus minimal 8 karakter, mengandung huruf besar, angka, dan simbol.";
    }

    // Jika tidak ada error
    if (empty($errors)) {
        // Simulasi simpan ke database (ganti sesuai kebutuhan Anda)
        // mysqli_query($conn, "INSERT INTO users ...");

        $success = "Registrasi berhasil! Silakan login.";
        // Kosongkan form input setelah sukses
        $_POST = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
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

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $err): ?>
                                        <li><?= htmlspecialchars($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <input type="hidden" name="action" value="register">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="name" required
                                        value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" required
                                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" name="address" required
                                    value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone"
                                    pattern="^\d{9,12}$"
                                    title="Masukkan 9 hingga 12 digit angka"
                                    required
                                    value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" name="username" required
                                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="login.php" class="btn btn-secondary me-md-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Create Account</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>