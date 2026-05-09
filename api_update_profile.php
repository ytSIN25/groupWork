<?php
// ============================================
// LUMIÈRE - Update Profile API (mysqli)
// POST { name, email }
// Returns JSON { success, user?, message? }
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

$name  = trim($input['name']  ?? '');
$email = trim($input['email'] ?? '');

if ($name === '' || $email === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name and email are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Check if email is taken by another user
$stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ? AND user_id != ? LIMIT 1');
$stmt->bind_param('si', $email, $_SESSION['user_id']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'This email is already in use']);
    exit;
}
$stmt->close();

// Update user
$stmt = $conn->prepare('UPDATE users SET name = ?, email = ? WHERE user_id = ?');
$stmt->bind_param('ssi', $name, $email, $_SESSION['user_id']);
$stmt->execute();
$stmt->close();

// Return updated user
$stmt = $conn->prepare('SELECT user_id, name, email, role, tier, avatar, created_at FROM users WHERE user_id = ?');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

echo json_encode([
    'success' => true,
    'user'    => $user,
]);
