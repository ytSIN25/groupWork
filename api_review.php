<?php
// ============================================
// LUMIÈRE - Get Reviews API
// GET movie_id
// Returns JSON { success, reviews, avg_rating, total, distribution, user_review, logged_in, user_id }
// ============================================

session_start();
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

$mid = intval($_GET['movie_id'] ?? 0);
if (!$mid) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing movie_id']);
    exit();
}

// Fetch all reviews with user info
$stmt = $conn->prepare("
    SELECT r.rating_id, r.star_num, r.content, r.user_id, u.name, u.avatar
    FROM ratings r
    JOIN users u ON r.user_id = u.user_id
    WHERE r.movie_id = ?
    ORDER BY r.rating_id DESC
");
$stmt->bind_param("i", $mid);
$stmt->execute();
$result = $stmt->get_result();
$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

// Average rating & total count
$avg_stmt = $conn->prepare("SELECT AVG(star_num) as avg_rating, COUNT(*) as total_reviews FROM ratings WHERE movie_id = ?");
$avg_stmt->bind_param("i", $mid);
$avg_stmt->execute();
$stats = $avg_stmt->get_result()->fetch_assoc();
$avg_rating    = $stats['avg_rating'] ? round($stats['avg_rating'], 1) : 0;
$total_reviews = intval($stats['total_reviews'] ?? 0);

// Star distribution
$dist_stmt = $conn->prepare("
    SELECT star_num, COUNT(*) as count
    FROM ratings
    WHERE movie_id = ?
    GROUP BY star_num
    ORDER BY star_num DESC
");
$dist_stmt->bind_param("i", $mid);
$dist_stmt->execute();
$dist_result = $dist_stmt->get_result();
$star_dist = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
while ($d = $dist_result->fetch_assoc()) {
    $star_dist[intval($d['star_num'])] = intval($d['count']);
}

// Current user's existing review (if logged in)
$user_review = null;
if (isset($_SESSION['user_id'])) {
    $my_stmt = $conn->prepare("SELECT star_num, content FROM ratings WHERE user_id = ? AND movie_id = ?");
    $my_stmt->bind_param("ii", $_SESSION['user_id'], $mid);
    $my_stmt->execute();
    $row = $my_stmt->get_result()->fetch_assoc();
    if ($row) {
        $user_review = $row;
    }
}

echo json_encode([
    'success'      => true,
    'reviews'      => $reviews,
    'avg_rating'   => $avg_rating,
    'total'        => $total_reviews,
    'distribution' => $star_dist,
    'user_review'  => $user_review,
    'logged_in'    => isset($_SESSION['user_id']),
    'user_id'      => $_SESSION['user_id'] ?? null
]);
