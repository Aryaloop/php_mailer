<?php
include '../includes/config.php';
require_once '../includes/auth.php'; // <--- Tambahkan ini

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] == 'register') {
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

    if (empty($errors)) {
        try {
            $success = registerUser($conn, $name, $email, $address, $phone, $username, $password);
            $_POST = [];
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}
?>

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

                        <?php if (!empty($errors)) : ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php foreach ($errors as $error) : ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php elseif ($success) : ?>
                            <div class="alert alert-success">Pendaftaran berhasil!</div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <input type="hidden" name="action" value="register">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" name="address" id="address" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" id="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="password">
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="index.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Register</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
