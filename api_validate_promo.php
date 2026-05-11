<?php
// ============================================
// LUMIÈRE - Validate Promo Code API
// GET code, total
// Returns JSON { success, promotion_id, discount, description } or { success, error }
// ============================================

require_once 'config.php';

header('Content-Type: application/json');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorised.']);
    exit;
}

// Only accept GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

$code  = trim($_GET['code']  ?? '');
$total = floatval($_GET['total'] ?? 0);

if ($code === '') {
    echo json_encode(['success' => false, 'error' => 'Please enter a promo code.']);
    exit;
}

$stmt = $conn->prepare("
    SELECT promotion_id, discount_value, description, minimum_spend
    FROM   promotions
    WHERE  promo_code = ? AND is_active = 1
");
$stmt->bind_param('s', $code);
$stmt->execute();
$promo = $stmt->get_result()->fetch_assoc();

if (!$promo) {
    echo json_encode(['success' => false, 'error' => 'Invalid or expired promo code.']);
    exit;
}

if ($total < $promo['minimum_spend']) {
    echo json_encode([
        'success' => false,
        'error'   => 'Minimum spend of €' . number_format($promo['minimum_spend'], 2) . ' required.',
    ]);
    exit;
}

echo json_encode([
    'success'      => true,
    'promotion_id' => $promo['promotion_id'],
    'discount'     => floatval($promo['discount_value']),
    'description'  => $promo['description'],
]);