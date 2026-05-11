<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index_login.php');
    exit();
}

$uid = $_SESSION['user_id'];

// --- NEW REFUND LOGIC ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['refund_order_id'])) {
    $order_to_refund = $_POST['refund_order_id'];
    
    // Security: Only delete if it belongs to the user and is a future show
    $delete_sql = "DELETE FROM orders WHERE order_id = ? AND user_id = ? AND show_date >= CURDATE()";
    $del_stmt = $conn->prepare($delete_sql);
    $del_stmt->bind_param("ii", $order_to_refund, $uid);
    
    if ($del_stmt->execute()) {
        $msg = "Ticket refunded successfully.";
    }
    // Refresh the page to update the list
    header("Location: history.php"); 
    exit();
}

$sql = "SELECT o.*, m.movie_name 
        FROM orders o 
        JOIN movies m ON o.movie_id = m.movie_id 
        WHERE o.user_id = ? 
        ORDER BY 
          CASE WHEN o.show_date >= CURDATE() THEN 0 ELSE 1 END,
          CASE WHEN o.show_date >= CURDATE() THEN o.show_date END ASC,
          CASE WHEN o.show_date >= CURDATE() THEN o.show_time END ASC,
          o.show_date DESC, 
          o.show_time DESC";
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
    <title>LUMIÈRE - My Tickets</title>
    <link rel="stylesheet" href="css/base.css?v=5">
    <link rel="stylesheet" href="css/pages/dashboard.css?v=5">
    <link rel="stylesheet" href="css/pages/footer.css?v=5">
    <link rel="stylesheet" href="css/global.css?v=5">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <style>
        .refund-btn {
            background: transparent;
            border: 1px solid var(--sunset-coral);
            color: var(--sunset-coral);
            padding: 5px 12px;
            font-family: var(--font-accent);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 4px;
            margin-top: 8px;
            pointer-events: auto !important;
        }

        .refund-btn:hover {
            background: var(--sunset-coral);
            color: white;
            box-shadow: 0 0 10px rgba(255, 126, 95, 0.4);
        }

        .stub-back {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 5;
        }

        /* --- Custom Confirmation Modal --- */
        .lumiere-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(13, 11, 14, 0.95);
            backdrop-filter: blur(10px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .lumiere-modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .lumiere-modal {
            background: var(--bg-card);
            border: 1px solid rgba(212, 168, 83, 0.2);
            padding: 40px;
            border-radius: 12px;
            max-width: 450px;
            width: 90%;
            text-align: center;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.8);
            transform: translateY(20px);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .lumiere-modal-overlay.active .lumiere-modal {
            transform: translateY(0);
        }

        .lumiere-modal::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--gold), var(--sunset-coral));
        }

        .lumiere-modal h3 {
            font-family: var(--font-display);
            font-size: 1.8rem;
            color: var(--gold);
            margin-bottom: 15px;
            font-style: italic;
        }

        .lumiere-modal p {
            color: #ddd;
            font-size: 1rem;
            line-height: 1.5;
            margin-bottom: 30px;
            opacity: 0.8;
        }

        .modal-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .modal-btn {
            padding: 12px 25px;
            border-radius: 6px;
            font-family: var(--font-accent);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 120px;
        }

        .modal-btn-confirm {
            background: var(--sunset-coral);
            border: none;
            color: white;
        }

        .modal-btn-confirm:hover {
            background: #ff6a4d;
            box-shadow: 0 0 20px rgba(232, 115, 90, 0.4);
            transform: translateY(-2px);
        }

        .modal-btn-cancel {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }

        .modal-btn-cancel:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: white;
        }
    </style>
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
            <a href="dashboard_user.php" class="nav-link">Account</a>
            <a href="about.php" class="nav-link">The Cinema</a>
        </div>
    </nav>

    <div class="page-wrapper timeline-section">
        <div class="timeline">
            <h2 class="fade-up" style="background:var(--bg-dark); padding:12px 30px; z-index:10; border-radius:6px; text-align:center;">Your Cinematographic Journey</h2>

            <?php while($order = mysqli_fetch_assoc($res)): 
                $show_timestamp = strtotime($order['show_date']);
                $today_timestamp = strtotime(date('Y-m-d'));
                $is_passed = $show_timestamp < $today_timestamp;?>
            <div class="timeline-item reveal">
                <div class="stub-card <?= $is_passed ? 'is-passed' : '' ?>" style="<?= $is_passed ? 'pointer-events: none; opacity: 0.5; filter: grayscale(100%);' : '' ?>">
                    <div class="stub-card-inner">
                        <div class="stub-front">
                            <div class="stub-theatre">Lumière Cinema - Paris</div>
                            <h3 class="stub-title">
                                <?= htmlspecialchars($order['movie_name']) ?>
                            </h3>

                            <div class="stub-meta">
                                <?= date('d M Y', strtotime($order['show_date'])) ?> • <?= substr($order['show_time'], 0, 5) ?> • SEATS: <?= htmlspecialchars($order['seats']) ?><br>
                                <span style="color:var(--gold); font-family:var(--font-accent); font-style:italic;">TIER: <?= htmlspecialchars($order['ticket_tier'] ?? 'Stalls') ?></span>
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
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://youtu.be/QDia3e12czc" alt="QR Code" style="width:80px; height:80px; border-radius:8px; margin-bottom:8px;">
                            <h3>Digital Pass</h3>
                            
                            <?php if (!$is_passed): ?>
                                <form method="POST" class="refund-form">
                                    <input type="hidden" name="refund_order_id" value="<?= $order['order_id'] ?>">
                                    <button type="button" class="refund-btn" onclick="showRefundConfirm(this.form)">Refund Ticket</button>
                                </form>
                            <?php else: ?>
                                <p style="font-size:0.75rem; color:var(--retro-red); border:1px solid; padding:2px 5px;">EXPIRED</p>
                            <?php endif; ?>
                            
                            <p style="font-size:0.7rem; font-style:italic; opacity:0.6; margin-top:5px;">Flip to show at the gate</p>
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
            <a href="dashboard_user.php">Account</a>
            <a href="dashboard_admin.php">Staff Area</a>
        </div>
        <p style="margin-top:30px; font-size:0.9rem; opacity:0.5;">© 2026 LUMIÈRE Cinemas. All rights reserved.</p>
    </footer>

    <div class="lumiere-modal-overlay" id="refundModal">
        <div class="lumiere-modal">
            <h3>Final Curtain?</h3>
            <p>Do you wish to cancel this booking? This action is permanent and cannot be undone.</p>
            <div class="modal-actions">
                <button class="modal-btn modal-btn-cancel" onclick="closeRefundModal()">Keep Ticket</button>
                <button class="modal-btn modal-btn-confirm" id="confirmRefundBtn">Refund Now</button>
            </div>
        </div>
    </div>

    <script>
        let formToSubmit = null;

        function showRefundConfirm(form) {
            formToSubmit = form;
            const modal = document.getElementById('refundModal');
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('active'), 10);
        }

        function closeRefundModal() {
            const modal = document.getElementById('refundModal');
            modal.classList.remove('active');
            setTimeout(() => modal.style.display = 'none', 400);
            formToSubmit = null;
        }

        document.getElementById('confirmRefundBtn').addEventListener('click', function() {
            if (formToSubmit) {
                formToSubmit.submit();
            }
        });

        document.getElementById('refundModal').addEventListener('click', function(e) {
            if (e.target === this) closeRefundModal();
        });
    </script>
    <script src="js/main.js?v=5"></script>
</body>
</html>