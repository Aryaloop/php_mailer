<?php
// PASTIKAN INCLUDE PATH BENAR
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/bookmark_functions.php';

// Cek session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data user dari database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Ambil data bookmark user
$bookmarks = getUserBookmarks($user_id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookmarks - T Informatica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <!-- Load Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <!-- NAVBAR - COPY PASTE DARI DASHBOARD.PHP -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">T Informatica</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="bookmarks.php"><i class="fas fa-bookmark"></i> Bookmarks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../includes/auth.php?logout=true">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Konten Bookmark -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <!-- Sidebar profile (sama seperti dashboard) -->
                <div class="card mb-4 text-center">
                    <div class="card-body">
                        <img src="https://via.placeholder.com/150" class="rounded-circle mb-3" alt="Profile Picture">
                        <h5 class="card-title"><?php echo htmlspecialchars($user['name']); ?></h5>
                        <p class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-bookmark"></i> My Bookmarked Movies</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($bookmarks)): ?>
                            <div class="row">
                                <?php foreach ($bookmarks as $bookmark): ?>
                                    <div class="col-md-4 col-lg-3 mb-4">
                                        <div class="card movie-card" onclick="window.location.href='dashboard.php?movie_id=<?php echo $bookmark['movie_id']; ?>'">
                                            <img src="<?php echo TMDB_IMAGE_URL . $bookmark['poster_path']; ?>" class="card-img-top movie-poster" alt="<?php echo htmlspecialchars($bookmark['movie_title']); ?>">
                                            <div class="card-body">
                                                <h6 class="card-title"><?php echo htmlspecialchars($bookmark['movie_title']); ?></h6>
                                                <button class="btn btn-sm btn-danger remove-bookmark"
                                                    data-movie-id="<?php echo $bookmark['movie_id']; ?>">
                                                    <i class="fas fa-trash-alt"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> You haven't bookmarked any movies yet.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk menghapus bookmark
        document.querySelectorAll('.remove-bookmark').forEach(button => {
            button.addEventListener('click', async function(e) {
                e.stopPropagation();
                const movieId = this.dataset.movieId;
                const card = this.closest('.col-md-4');

                try {
                    const response = await fetch('../includes/bookmark_handler.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'remove',
                            movie_id: movieId
                        })
                    });

                    if (response.ok) {
                        card.remove();
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            });
        });
    </script>
</body>

</html>