<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index_login.php');
    exit();
}
$uid = $_SESSION['user_id'];

$sql = "SELECT o.*, m.movie_name 
        FROM orders o 
        JOIN movies m ON o.movie_id = m.movie_id 
        WHERE o.user_id = ? 
        ORDER BY o.order_id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $uid);
$stmt->execute();
$res = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUMIÈRE — My Tickets</title>
    <link rel="stylesheet" href="css/base.css?v=5">
    <link rel="stylesheet" href="css/pages/dashboard.css?v=5">
    <link rel="stylesheet" href="css/pages/footer.css?v=5">
    <link rel="stylesheet" href="css/global.css?v=5">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
</head>

<body>
    <div class="film-grain"></div>
    <div class="page-transition active" id="pageTransition">
        <span class="trans-logo">LUMIÈRE</span>
    </div>

    <nav class="lumiere-nav">
        <a href="movies.php" class="lumiere-logo">
            <img src="assets/images/logo.svg?v=5" alt="LUMIÈRE">
        </a>

        <div class="nav-links">
            <a href="index.php" class="nav-link">Home</a>
            <a href="movies.php" class="nav-link">Now Showing</a>
            <a href="history.php" class="nav-link" style="color:var(--sunset-coral);">My Tickets</a>
            <a href="dashboard-user.php" class="nav-link">Account</a>
            <a href="about.php" class="nav-link">The Cinema</a>
        </div>
    </nav>

    <div class="page-wrapper timeline-section">
        <div class="timeline">
            <h2 class="fade-up" style="background:var(--bg-dark); padding:12px 30px; z-index:10; border-radius:6px; text-align:center; border:1px solid rgba(212,168,83,0.15);">Your Cinematographic Journey</h2>

            <?php while($order = mysqli_fetch_assoc($res)): 
                $show_timestamp = strtotime($order['show_date']);
                $today_timestamp = strtotime(date('Y-m-d'));
                $is_passed = $show_timestamp < $today_timestamp;?>
            <div class="timeline-item reveal">
                <div class="stub-card <?= $is_passed ? 'is-passed' : '' ?>" style="<?= $is_passed ? 'pointer-events: none;' : '' ?>">
                    <div class="stub-card-inner">
                        <div class="stub-front">
                            <div class="stub-theatre">Lumière Cinema — Paris</div>
                            <h3 class="stub-title">
                                <?= htmlspecialchars($order['movie_name']) ?>
                            </h3>

                            <div class="stub-meta">
                                <?= date('d M Y', strtotime($order['show_date'])) ?> • <?= substr($order['show_time'], 0, 5) ?> • SEATS: <?= $order['seats'] ?>
                            </div>

                            <?php if ($is_passed): ?>
                                <div style="margin-top:10px; font-weight:700; color:#888; border:1px solid #888; padding:2px 8px; font-size:0.75rem; border-radius:3px; display:inline-block; opacity:0.7;">
                                    PASSED
                                </div>
                            <?php else: ?>
                                <div style="margin-top:10px; font-weight:700; color:var(--retro-red); border:1px solid var(--retro-red); padding:2px 8px; font-size:0.75rem; border-radius:3px; display:inline-block;">
                                    CONFIRMED
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="stub-back">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://youtu.be/dQw4w9WgXcQ" alt="QR Code" style="width:100px; height:100px; border-radius:8px; margin-bottom:10px;">
                            <h3>Digital Pass</h3>
                            <p style="font-size:0.8rem; font-style:italic; opacity:0.6;">Flip to show at the gate</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <footer>
        <img src="assets/images/logo.svg?v=5" alt="LUMIÈRE" class="logo-img">
        <p>Where Every Seat Tells a Story.</p>
        <div class="footer-links">
            <a href="movies.php">Now Showing</a>
            <a href="about.php">The Cinema</a>
            <a href="dashboard-user.php">Account</a>
            <a href="dashboard-admin.php">Staff Area</a>
        </div>
        <p style="margin-top:30px; font-size:0.9rem; opacity:0.5;">© 2026 LUMIÈRE Cinemas. All rights reserved.</p>
    </footer>

    <script src="js/main.js?v=5"></script>
</body>
</html>