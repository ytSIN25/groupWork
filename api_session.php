<?php
// ============================================
// LUMIÈRE - Session API
// Returns JSON { success, user? }
// ============================================

header('Content-Type: application/json');
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No active session']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch latest user data
$stmt = $conn->prepare('SELECT user_id, name, email, role, tier, avatar FROM users WHERE user_id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user) {
    // Add some mock stats if not in DB yet
    $user['films_this_year'] = 12;
    $user['points'] = 2840;
    
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    // Session exists but user deleted
    session_destroy();
    echo json_encode(['success' => false, 'message' => 'User not found']);
}
