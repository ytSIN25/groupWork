<?php
// ============================================
// LUMIÈRE - Database Configuration (mysqli)
// ============================================

$DB_HOST = 'localhost';
$DB_NAME = 'lumiere_cinema';
$DB_USER = 'root';
$DB_PASS = '';

// Standard Ticket Price for all Cinematic Experiences
define('LUMIERE_BASE_PRICE', 15.00);

// This replaces manual error_reporting(E_ALL) and shows errors in our cinematic error.php
function cinematic_error_handler($level, $message, $file, $line) {
    if (!(error_reporting() & $level)) return;
    $details = "Error: $message\nFile: $file\nLine: $line";
    if (!headers_sent()) {
        header('Location: error.php?code=500&msg=Script Malfunction&details=' . urlencode($details));
        exit;
    }
}

function cinematic_exception_handler($e) {
    $details = "Exception: " . $e->getMessage() . "\nFile: " . $e->getFile() . "\nLine: " . $e->getLine() . "\n\nStack Trace:\n" . $e->getTraceAsString();
    if (!headers_sent()) {
        header('Location: error.php?code=500&msg=Unexpected Plot Twist&details=' . urlencode($details));
        exit;
    }
}

// Catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $details = "Fatal Error: " . $error['message'] . "\nFile: " . $error['file'] . "\nLine: " . $error['line'];
        if (!headers_sent()) {
            header('Location: error.php?code=500&msg=Projector Total Failure&details=' . urlencode($details));
        } else {
            echo "<script>window.location.href='error.php?code=500&msg=Projector Total Failure&details=' + encodeURIComponent(".json_encode($details).");</script>";
        }
    }
});

set_error_handler("cinematic_error_handler");
set_exception_handler("cinematic_exception_handler");
error_reporting(E_ALL); // We catch everything now

// mysqli connection
mysqli_report(MYSQLI_REPORT_OFF); 
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    // API requests still get JSON
    if (strpos($_SERVER['PHP_SELF'], 'api_') !== false) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    } else {
        // Web pages get the cinematic error page
        header('Location: error.php?code=db&msg=Archive Connection Lost&details=' . urlencode($conn->connect_error));
    }
    exit;
}

// Start session on every request
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
