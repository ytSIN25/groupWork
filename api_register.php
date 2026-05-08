<?php
// ============================================
// LUMIÈRE - Registration API
// POST { name, email, password }
// Returns JSON { success, message? }
// ============================================

header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$name     = trim($input['name']     ?? '');
$email    = trim($input['email']    ?? '');
$password = $input['password']      ?? '';

if ($name === '' || $email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Insert into DB
$stmt = $conn->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, "patron")');
$stmt->bind_param('sss', $name, $email, $hashed_password);

if ($stmt->execute()) {
    $new_user_id = $conn->insert_id;
    
    // Auto-login
    $_SESSION['user_id']   = $new_user_id;
    $_SESSION['user_role'] = 'patron';

    echo json_encode(['success' => true, 'message' => 'Registration successful']);
} else {
    if ($conn->errno === 1062) {
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
}
$stmt->close();
