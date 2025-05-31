<?php
// includes/bookmark_functions.php

require_once __DIR__ . '/config.php';

function addBookmark($user_id, $movie_id, $movie_title, $poster_path)
{
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO bookmarks (user_id, movie_id, movie_title, poster_path) 
                           VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $user_id, $movie_id, $movie_title, $poster_path);
    return $stmt->execute();
}

function removeBookmark($user_id, $movie_id)
{
    global $conn;
    $stmt = $conn->prepare("DELETE FROM bookmarks WHERE user_id = ? AND movie_id = ?");
    $stmt->bind_param("ii", $user_id, $movie_id);
    return $stmt->execute();
}

function isBookmarked($user_id, $movie_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM bookmarks WHERE user_id = ? AND movie_id = ?");
    $stmt->bind_param("ii", $user_id, $movie_id);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function getUserBookmarks($user_id)
{
    global $conn;
    
    $stmt = $conn->prepare("SELECT 
        id, user_id, movie_id, movie_title, poster_path, created_at
        FROM bookmarks 
        WHERE user_id = ? 
        ORDER BY created_at DESC");
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    $bookmarks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Tambahkan base URL jika belum ada
    foreach ($bookmarks as &$bookmark) {
        if (!empty($bookmark['poster_path']) && !str_starts_with($bookmark['poster_path'], 'http')) {
            $bookmark['poster_path'] = TMDB_IMAGE_URL . ltrim($bookmark['poster_path'], '/');
        }
    }
    
    return $bookmarks;
}