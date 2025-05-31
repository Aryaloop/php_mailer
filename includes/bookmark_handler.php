<?php
// includes/bookmark_handler.php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/bookmark_functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];

try {
    if (!isset($input['action']) || !in_array($input['action'], ['add', 'remove'])) {
        throw new Exception('Invalid action');
    }

    $response = [];
    
    if ($input['action'] === 'add') {
        if (empty($input['movie_id']) || empty($input['movie_title']) || empty($input['poster_path'])) {
            throw new Exception('Missing required fields');
        }

        $result = addBookmark(
            $user_id,
            (int)$input['movie_id'],
            htmlspecialchars($input['movie_title'], ENT_QUOTES, 'UTF-8'),
            $input['poster_path']
        );
        
        $response = ['success' => $result, 'message' => $result ? 'Bookmark added' : 'Failed to add'];
    } 
    elseif ($input['action'] === 'remove') {
        if (empty($input['movie_id'])) {
            throw new Exception('Missing movie ID');
        }

        $result = removeBookmark($user_id, (int)$input['movie_id']);
        $response = ['success' => $result, 'message' => $result ? 'Bookmark removed' : 'Failed to remove'];
    }

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}