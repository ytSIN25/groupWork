<?php
// ============================================
// LUMIÈRE - Delete Review API
// POST { movie_id }
// Returns JSON { success, message }
// ============================================

session_start();
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'You must be logged in.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$user_id  = $_SESSION['user_id'];
$movie_id = intval($data['movie_id'] ?? 0);

if (!$movie_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid movie.']);
    exit();
}

// Delete the user's review for this movie
$del = $conn->prepare("DELETE FROM ratings WHERE user_id = ? AND movie_id = ?");
$del->bind_param("ii", $user_id, $movie_id);
$del->execute();

if ($del->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Review deleted.']);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'No review found to delete.']);
}
