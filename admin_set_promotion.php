<?php
require_once 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index_login.php');
    exit();
}

$message = "";

// 1. Handle Promotion Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mint_promo'])) {
    $promo_code     = strtoupper(trim($_POST['promo_code']));
    $discount_value = $_POST['discount_value'] ?? 0.00;
    $description    = $_POST['description'] ?? "";
    $min_spend      = $_POST['minimum_spend'] ?? 0.00;
    $user_id        = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO promotions (user_id, promo_code, discount_value, description, minimum_spend, is_active) VALUES (?, ?, ?, ?, ?, 1)");
    if ($stmt) {
        $stmt->bind_param("isdsd", $user_id, $promo_code, $discount_value, $description, $min_spend);
        if ($stmt->execute()) {
            $message = "Coupon minted successfully!";
        } else {
            $message = "Error: Code might already exist.";
        }
        $stmt->close();
    }
}

// 2. Handle Promotion Deletion
if (isset($_GET['delete_id'])) {
    $del_id = $_GET['delete_id'];
    $conn->query("DELETE FROM promotions WHERE promotion_id = $del_id");
    header('Location: admin_set_promotion.php');
    exit();
}

// 3. Handle Status Toggle
if (isset($_GET['toggle_id'])) {
    $tid = $_GET['toggle_id'];
    $conn->query("UPDATE promotions SET is_active = 1 - is_active WHERE promotion_id = $tid");
    header('Location: admin_set_promotion.php');
    exit();
}

// 4. Fetch All Promotions
$promos = [];
$res = $conn->query("SELECT * FROM promotions ORDER BY promotion_id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $promos[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUMIÃˆRE â€” Promotions</title>
    <link rel="stylesheet" href="css/base.css?v=5">
    <link rel="stylesheet" href="css/pages/dashboard.css?v=5">
    <link rel="stylesheet" href="css/pages/footer.css?v=5">
    <link rel="stylesheet" href="css/global.css?v=5">
    <style>
        .promo-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-top: 25px;
        }

        .promo-card {
            border: 2px dashed rgba(212, 168, 83, 0.3);
            padding: 25px;
            background: var(--bg-card);
            text-align: center;
            position: relative;
            border-radius: 6px;
            transition: var(--transition);
        }

        .promo-card.inactive {
            opacity: 0.5;
            border-style: solid;
            border-color: #444;
        }

        .promo-card:hover {
            border-color: var(--sunset-coral);
            transform: translateY(-5px);
        }

        .promo-card::before,
        .promo-card::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 25px;
            height: 25px;
            background: var(--bg-dark);
            border-radius: 50%;
            transform: translateY(-50%);
        }

        .promo-card::before {
            left: -13px;
        }

        .promo-card::after {
            right: -13px;
        }

        .promo-stamp {
            position: absolute;
            right: 15px;
            top: 15px;
            color: var(--gold);
            font-size: 0.8rem;
            font-weight: 700;
            transform: rotate(-12deg);
            border: 1px solid var(--gold);
            padding: 2px 8px;
            letter-spacing: 0.1em;
        }

        .promo-stamp.inactive-stamp {
            color: var(--sunset-coral);
            border-color: var(--sunset-coral);
        }

        .control-btns {
            position: absolute;
            left: 15px;
            top: 15px;
            display: flex;
            gap: 10px;
        }

        .admin-action-link {
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 600;
            transition: 0.3s;
        }

        .retire-link { color: var(--sunset-coral); opacity: 0.6; }
        .retire-link:hover { opacity: 1; }
        
        .toggle-link { color: var(--retro-mint, #88c0d0); opacity: 0.8; }
        .toggle-link:hover { opacity: 1; }
    </style>
</head>

<body>
    <div class="film-grain"></div>
    <div class="page-transition active" id="pageTransition">
        <span class="trans-logo">LUMIÃˆRE</span>
    </div>

    <nav class="lumiere-nav" style="padding: 15px 5%;">
        <a href="dashboard_admin.php" class="lumiere-logo" style="gap: 10px;">
            <img src="assets/images/logo.svg?v=5" alt="LUMIÃˆRE" style="height: 40px;">
            <span style="font-family: var(--font-accent); font-size: 1rem; color: var(--mocha); letter-spacing: 0.2em;">STAFF</span>
        </a>
        <div class="nav-links">
            <a href="dashboard_admin.php" class="nav-link">Dashboard</a>
            <a href="admin_add_movie.php" class="nav-link">Catalog</a>
            <a href="admin_set_promotion.php" class="nav-link" style="color: var(--sunset-coral);">Promotions</a>
            <a href="movies.php" class="nav-link" style="color: var(--sunset-rose);">Exit Staff</a>
        </div>
    </nav>

    <div class="page-wrapper org-wrapper" style="padding: 120px 5% 60px; display: flex; gap: 50px;">
        <!-- Form Column -->
        <div style="flex: 1;">
            <h1 class="fade-up" style="margin-bottom: 8px;">Issue Promotion</h1>
            <p style="color: var(--mocha); font-style: italic; font-family: var(--font-accent); margin-bottom: 30px;" class="fade-up">
                Generate coupons for patron redemption.
            </p>
            <div style="background: var(--cream); color: var(--bg-deep); padding: 40px; border-radius: 8px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);" class="scale-in">
                <form action="" method="POST">
                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex: 1;">
                            <label style="color: var(--bg-deep); font-weight: 600;">Promo Code</label>
                            <input type="text" name="promo_code" class="typewriter-input" style="color: var(--bg-deep); text-transform: uppercase;" placeholder="e.g. GOLD20" required>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label style="color: var(--bg-deep); font-weight: 600;">Discount Value (ÂRM)</label>
                            <input type="number" step="0.01" name="discount_value" class="typewriter-input" style="color: var(--bg-deep);" placeholder="5.00" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label style="color: var(--bg-deep); font-weight: 600;">Description</label>
                        <input type="text" name="description" class="typewriter-input" style="color: var(--bg-deep);" placeholder="e.g. Student Appreciation Discount">
                    </div>

                    <div class="form-group">
                        <label style="color: var(--bg-deep); font-weight: 600;">Minimum Spend (ÂRM)</label>
                        <input type="number" step="0.01" name="minimum_spend" class="typewriter-input" style="color: var(--bg-deep);" placeholder="0.00">
                    </div>

                    <button class="btn-primary" name="mint_promo" style="width: 100%; margin-top: 15px; color: var(--bg-deep); border-color: var(--bg-deep);" type="submit">
                        Mint Coupon
                    </button>
                </form>
            </div>
        </div>

        <!-- List Column -->
        <div style="flex: 1;">
            <h2 class="fade-up" style="margin-bottom: 15px;">Active Ledgers</h2>
            <div class="promo-list">
                <?php if (empty($promos)): ?>
                    <div style="grid-column: span 2; text-align: center; color: var(--mocha); padding: 40px; border: 1px dashed rgba(212, 168, 83, 0.2); border-radius: 8px;">
                        No coupons in the ledger yet.
                    </div>
                <?php else: ?>
                    <?php foreach ($promos as $p): ?>
                        <div class="promo-card fade-up <?php echo $p['is_active'] ? '' : 'inactive'; ?>">
                            <div class="control-btns">
                                <a href="?toggle_id=<?php echo $p['promotion_id']; ?>" class="admin-action-link toggle-link">
                                    <?php echo $p['is_active'] ? 'â¸ Pause' : 'â–¶ Resume'; ?>
                                </a>
                                <a href="?delete_id=<?php echo $p['promotion_id']; ?>" class="admin-action-link retire-link" onclick="return confirm('Retire this coupon?')">âœ• Retire</a>
                            </div>
                            
                            <?php if ($p['is_active']): ?>
                                <div class="promo-stamp">ACTIVE</div>
                            <?php else: ?>
                                <div class="promo-stamp inactive-stamp">PAUSED</div>
                            <?php endif; ?>

                            <h3 style="color: var(--gold); font-size: 1.4rem;">ÂRM<?php echo number_format($p['discount_value'], 2); ?> OFF</h3>
                            <p style="color: var(--mocha); font-style: italic; font-size: 0.9rem; margin-top: 8px;">
                                <?php echo htmlspecialchars($p['description']); ?>
                            </p>
                            <div style="font-family: monospace; border-top: 1px dashed rgba(212, 168, 83, 0.2); margin-top: 12px; padding-top: 10px; letter-spacing: 0.1em; color: var(--cream);">
                                <?php echo htmlspecialchars($p['promo_code']); ?>
                            </div>
                            <div style="font-size: 0.7rem; color: var(--mocha); margin-top: 5px;">Min Spend: ÂRM<?php echo number_format($p['minimum_spend'], 2); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/main.js?v=5"></script>

    <?php if ($message !== ""): ?>
    <script>
        Swal.fire({
            title: 'Success',
            text: '<?php echo $message; ?>',
            icon: 'success',
            background: '#F2E8D5',
            color: '#0D0B0E',
            confirmButtonColor: '#2A7A7A'
        });
    </script>
    <?php endif; ?>
</body>

</html>
