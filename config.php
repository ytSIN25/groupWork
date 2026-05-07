<?php
// ============================================
// LUMIÈRE - Database Configuration (mysqli)
// ============================================

$DB_HOST = 'localhost';
$DB_NAME = 'lumiere_cinema';
$DB_USER = 'root';
$DB_PASS = '';

// mysqli connection
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Start session on every request
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
