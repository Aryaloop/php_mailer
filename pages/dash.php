<?php

include '../includes/config.php';

// Cek kalau user belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $conn->real_escape_string($_SESSION['user_id']);

// Ambil data user
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    // Jika user tidak ditemukan (mungkin akun dihapus)
    session_destroy();
    header("Location: login.php");
    exit();
}

$user = $result->fetch_assoc();

// Konfigurasi API Film
define('TMDB_API_KEY', '3097f4aed12eb128588745df1a12a5f0'); // Ganti dengan API key Anda
define('TMDB_BASE_URL', 'https://api.themoviedb.org/3');
define('TMDB_IMAGE_URL', 'https://image.tmdb.org/t/p/w500');

// Fungsi untuk mendapatkan film populer
function getPopularMovies($page = 1)
{
    $url = TMDB_BASE_URL . '/movie/popular?api_key=' . TMDB_API_KEY . '&page=' . $page;
    $response = file_get_contents($url);
    return json_decode($response, true);
}

// Fungsi untuk mencari film
function searchMovies($query, $page = 1)
{
    $url = TMDB_BASE_URL . '/search/movie?api_key=' . TMDB_API_KEY . '&query=' . urlencode($query) . '&page=' . $page;
    $response = file_get_contents($url);
    return json_decode($response, true);
}

// Fungsi untuk mendapatkan detail film
function getMovieDetails($movie_id)
{
    $url = TMDB_BASE_URL . '/movie/' . $movie_id . '?api_key=' . TMDB_API_KEY . '&append_to_response=videos';
    $response = file_get_contents($url);
    return json_decode($response, true);
}

// Proses pencarian
$movies = [];
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;

if (!empty($search_query)) {
    $movies_data = searchMovies($search_query, $current_page);
} else {
    $movies_data = getPopularMovies($current_page);
}

if (isset($movies_data['results'])) {
    $movies = $movies_data['results'];
    $total_pages = $movies_data['total_pages'];
}

// Proses detail film
$movie_details = null;
if (isset($_GET['movie_id'])) {
    $movie_details = getMovieDetails($_GET['movie_id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bookmark_action'])) {
    $movie_id = intval($_POST['movie_id']);
    if ($_POST['bookmark_action'] === 'add') {
        addBookmark($_SESSION['user_id'], $movie_id, $_POST['movie_title'], $_POST['poster_path']);
    } else {
        removeBookmark($_SESSION['user_id'], $movie_id);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - T Informatica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .movie-card {
            transition: transform 0.3s;
            margin-bottom: 20px;
            height: 100%;
        }

        .movie-card:hover {
            transform: scale(1.03);
            cursor: pointer;
        }

        .movie-poster {
            height: 300px;
            object-fit: cover;
        }

        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            /* 16:9 Aspect Ratio */
            height: 0;
            overflow: hidden;
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .back-button {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">T Informatica</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'bookmarks.php' ? 'active' : '' ?>" href="bookmarks.php">
                            <i class="fas fa-bookmark"></i> Bookmarks
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : '' ?>" href="profile.php">
                            <i class="fas fa-user"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../includes/auth.php?logout=true">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <!-- Sidebar Profile -->
            <div class="col-md-3">
                <div class="card mb-4 text-center">
                    <div class="card-body">
                        <img src="https://via.placeholder.com/150" class="rounded-circle mb-3" alt="Profile Picture">
                        <h5 class="card-title"><?php echo htmlspecialchars($user['name']); ?></h5>
                        <p class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></p>
                        <a href="profile.php" class="btn btn-outline-primary btn-sm">Edit Profile</a>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard -->
            <div class="col-md-9">
                <?php if ($movie_details): ?>
                    <!-- Tampilan Detail Film -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <a href="dashboard.php" class="btn btn-secondary back-button">Back to Movies</a>
                            <h4><?php echo htmlspecialchars($movie_details['title']); ?></h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <img src="<?php echo TMDB_IMAGE_URL . $movie_details['poster_path']; ?>" class="img-fluid rounded mb-3" alt="<?php echo htmlspecialchars($movie_details['title']); ?>">
                                </div>
                                <div class="col-md-8">
                                    <h5>Overview</h5>
                                    <p><?php echo htmlspecialchars($movie_details['overview']); ?></p>
                                    <p><strong>Release Date:</strong> <?php echo htmlspecialchars($movie_details['release_date']); ?></p>
                                    <p><strong>Rating:</strong> <?php echo htmlspecialchars($movie_details['vote_average']); ?>/10</p>

                                    <?php if (!empty($movie_details['videos']['results'])): ?>
                                        <h5 class="mt-4">Trailer</h5>
                                        <?php
                                        $trailer = null;
                                        foreach ($movie_details['videos']['results'] as $video) {
                                            if ($video['type'] == 'Trailer' && $video['site'] == 'YouTube') {
                                                $trailer = $video;
                                                break;
                                            }
                                        }
                                        if (!$trailer && !empty($movie_details['videos']['results'])) {
                                            $trailer = $movie_details['videos']['results'][0];
                                        }
                                        if ($trailer): ?>
                                            <div class="video-container mt-3">
                                                <iframe src="https://www.youtube.com/embed/<?php echo $trailer['key']; ?>" allowfullscreen></iframe>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Tampilan Daftar Film -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Movie Streaming</h4>
                            <form class="d-flex" method="GET" action="dashboard.php">
                                <input class="form-control me-2" type="search" name="search" placeholder="Search movies..." value="<?php echo htmlspecialchars($search_query); ?>">
                                <button class="btn btn-outline-success" type="submit">Search</button>
                            </form>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($search_query)): ?>
                                <h5>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h5>
                            <?php else: ?>
                                <h5>Popular Movies</h5>
                            <?php endif; ?>

                            <div class="row mt-4">
                                <?php if (!empty($movies)): ?>
                                    <?php foreach ($movies as $movie): ?>
                                        <?php if (!empty($movie['poster_path'])): ?>
                                            <div class="col-md-4 col-lg-3">
                                                <div class="card movie-card" onclick="window.location.href='dashboard.php?movie_id=<?php echo $movie['id']; ?>'">
                                                    <img src="<?php echo TMDB_IMAGE_URL . $movie['poster_path']; ?>" class="card-img-top movie-poster" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                                                    <div class="card-body">
                                                        <h6 class="card-title"><?php echo htmlspecialchars($movie['title']); ?></h6>
                                                        <p class="card-text text-muted">
                                                            <small><?php echo isset($movie['release_date']) ? substr($movie['release_date'], 0, 4) : 'N/A'; ?></small>
                                                            <span class="float-end"><small>⭐ <?php echo $movie['vote_average']; ?></small></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <div class="alert alert-info">No movies found.</div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <nav aria-label="Page navigation" class="mt-4">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($current_page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="dashboard.php?page=<?php echo $current_page - 1; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= min($total_pages, 5); $i++): ?>
                                            <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                                <a class="page-link" href="dashboard.php?page=<?php echo $i; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($current_page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="dashboard.php?page=<?php echo $current_page + 1; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>" aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Bagian Dashboard Asli (Tetap Ada) -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>