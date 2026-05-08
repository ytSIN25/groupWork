<?php
// ============================================
// LUMIÈRE - Session Check API
// Returns current logged-in user or error
// ============================================

header('Content-Type: application/json');
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Fetch fresh user data from database (in case of updates)
$stmt = $conn->prepare('SELECT * FROM users WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    session_destroy();
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

unset($user['password']); // Remove password before sending user data for security

echo json_encode([
    'success' => true,
    'user'    => $user,
]);
