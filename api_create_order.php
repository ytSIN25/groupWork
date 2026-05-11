<?php
// ============================================
// LUMIÈRE - Create Order API
// POST { movie_id, show_date, show_time, seats, n_seats, price, tier?, cc_number, cc_expiry, cc_cvc, promotion_id? }
// Returns JSON { success, order_id?, error? }
// ============================================

require_once 'config.php';

header('Content-Type: application/json');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorised.']);
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

// Parse input
$content_type = $_SERVER['CONTENT_TYPE'] ?? '';

if (str_contains($content_type, 'application/json')) {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $get  = fn(string $key, mixed $default = '') => $body[$key] ?? $default;
} else {
    $get  = fn(string $key, mixed $default = '') => $_POST[$key] ?? $default;
}

$movie_id     = intval($get('movie_id', 0));
$show_date    = $get('show_date');
$show_time    = $get('show_time');
$seats        = $get('seats');
$n_seats      = intval($get('n_seats', 0));
$price        = floatval($get('price', 0));
$tier         = $get('tier', 'Stalls');
$cc_number    = $get('cc_number');
$cc_expiry    = $get('cc_expiry');
$cc_cvc       = $get('cc_cvc');
$promo_id_raw = $get('promotion_id', '');
$promo_id     = $promo_id_raw !== '' ? intval($promo_id_raw) : null;

// Validate required fields
if (!$movie_id || empty($show_date) || empty($show_time) || empty($seats) || !$n_seats) {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'Incomplete booking data.']);
    exit;
}

// Insert order
ob_start();  // absorb any warnings that would corrupt JSON

try {
    // Check whether ticket_tier column exists
    $col_result   = $conn->query("SHOW COLUMNS FROM orders LIKE 'ticket_tier'");
    $has_tier_col = $col_result && $col_result->num_rows > 0;

    if ($has_tier_col) {
        $sql  = "INSERT INTO orders
                     (user_id, movie_id, promotion_id, show_date, show_time,
                      seats, num_seats, total_price, ticket_tier, cc_number, cc_expiry, cc_cvc)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) throw new Exception('Prepare failed: ' . $conn->error);
        $stmt->bind_param(
            'iiisssidssss',
            $_SESSION['user_id'], $movie_id, $promo_id,
            $show_date, $show_time,
            $seats, $n_seats, $price,
            $tier, $cc_number, $cc_expiry, $cc_cvc
        );
    } else {
        $sql  = "INSERT INTO orders
                     (user_id, movie_id, promotion_id, show_date, show_time,
                      seats, num_seats, total_price, cc_number, cc_expiry, cc_cvc)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) throw new Exception('Prepare failed: ' . $conn->error);
        $stmt->bind_param(
            'iiisssidsss',
            $_SESSION['user_id'], $movie_id, $promo_id,
            $show_date, $show_time,
            $seats, $n_seats, $price,
            $cc_number, $cc_expiry, $cc_cvc
        );
    }

    ob_end_clean();

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'order_id' => $conn->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

} catch (Exception $ex) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $ex->getMessage()]);
}