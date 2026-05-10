<?php
// ============================================
// LUMIÈRE - Create/Update Review API
// POST { movie_id, star_num, content }
// Returns JSON { success, action, message }
// ============================================

session_start();
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'You must be logged in to review.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$user_id  = $_SESSION['user_id'];
$movie_id = intval($data['movie_id'] ?? 0);
$star_num = intval($data['star_num'] ?? 0);
$content  = trim($data['content'] ?? '');

// Validation
if (!$movie_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid movie.']);
    exit();
}
if ($star_num < 1 || $star_num > 5) {
    http_response_code(400);
    echo json_encode(['error' => 'Rating must be between 1 and 5.']);
    exit();
}

// Check movie exists
$chk = $conn->prepare("SELECT movie_id FROM movies WHERE movie_id = ?");
$chk->bind_param("i", $movie_id);
$chk->execute();
if (!$chk->get_result()->fetch_assoc()) {
    http_response_code(404);
    echo json_encode(['error' => 'Movie not found.']);
    exit();
}

// Insert or Update
$existing = $conn->prepare("SELECT rating_id FROM ratings WHERE user_id = ? AND movie_id = ?");
$existing->bind_param("ii", $user_id, $movie_id);
$existing->execute();
$row = $existing->get_result()->fetch_assoc();

if ($row) {
    $update = $conn->prepare("UPDATE ratings SET star_num = ?, content = ? WHERE user_id = ? AND movie_id = ?");
    $update->bind_param("isii", $star_num, $content, $user_id, $movie_id);
    $update->execute();
    echo json_encode(['success' => true, 'action' => 'updated', 'message' => 'Review updated.']);
} else {
    $insert = $conn->prepare("INSERT INTO ratings (user_id, movie_id, star_num, content) VALUES (?, ?, ?, ?)");
    $insert->bind_param("iiis", $user_id, $movie_id, $star_num, $content);
    $insert->execute();
    echo json_encode(['success' => true, 'action' => 'created', 'message' => 'Review submitted.']);
}
