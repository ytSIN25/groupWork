<?php
// ============================================
// LUMIÈRE - Login API
// POST { email, password }
// Returns JSON { success, user?, message? }
// ============================================

header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$email    = trim($input['email'] ?? '');
$password = $input['password']   ?? '';

if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit;
}

// Look up user by email
$stmt = $conn->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Credentials not found in the Cinema Registry.']);
    exit;
}

// Verify hashed password
if (!password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Credentials not found in the Cinema Registry.']);
    exit;
}

// Success, store session data
$_SESSION['user_id']  = $user['user_id'];
$_SESSION['user_role'] = $user['role'];

// Return user data without password (for security)
unset($user['password']);
echo json_encode([
    'success' => true,
    'user'    => $user
]);
