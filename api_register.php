<?php
// ============================================
// LUMIÈRE - Register API
// POST { name, email, password }
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
$name     = trim($input['name']  ?? '');
$email    = trim($input['email'] ?? '');
$password = $input['password']   ?? '';

// Validation
if ($name === '' || $email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

if (strlen($password) < 4) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 4 characters']);
    exit;
}

// Check if email already exists
$stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {  // If email is already registered
    $stmt->close();
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'This email is already registered']);
    exit;
}
$stmt->close();

// Hash password
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Generate avatar URL
$avatar = 'https://api.dicebear.com/7.x/notionists/svg?seed=' . urlencode($name);

// Insert new patron
$role = 'patron';
$stmt = $conn->prepare(
    'INSERT INTO users (name, email, password, role, avatar) VALUES (?, ?, ?, ?, ?)'
);
$stmt->bind_param('sssss', $name, $email, $hashed, $role, $avatar);
$stmt->execute();
$newId = $conn->insert_id;
$stmt->close();

// Auto-login: store session
$_SESSION['user_id']  = $newId;
$_SESSION['user_role'] = 'patron';

// Return the new user
echo json_encode([
    'success' => true,
    'user'    => [
        'user_id' => $newId,
        'name'    => $name,
        'email'   => $email,
        'role'    => 'patron',
        'tier'    => 'Bronze Reel Member',
        'avatar'  => $avatar,
    ],
]);
