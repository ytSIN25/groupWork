<?php
// ============================================
// LUMIÈRE - Update Preferences API (mysqli)
// POST  { seating, snack, genre }
// Returns JSON { success, message? }
// ============================================

header('Content-Type: application/json');
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$seating = trim($input['seating'] ?? '');
$snack   = trim($input['snack']   ?? '');
$genre   = trim($input['genre']   ?? '');

// Check if preferences row exists
$stmt = $conn->prepare('SELECT user_id FROM user_preferences WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$stmt->store_result();
$exists = $stmt->num_rows > 0;
$stmt->close();

if ($exists) {
    $stmt = $conn->prepare('UPDATE user_preferences SET preferred_seating = ?, preferred_snack = ?, preferred_genre = ? WHERE user_id = ?');
    $stmt->bind_param('sssi', $seating, $snack, $genre, $_SESSION['user_id']);
} else {
    $stmt = $conn->prepare('INSERT INTO user_preferences (user_id, preferred_seating, preferred_snack, preferred_genre) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('isss', $_SESSION['user_id'], $seating, $snack, $genre);
}

$stmt->execute();
$stmt->close();

echo json_encode(['success' => true, 'message' => 'Preferences saved']);
