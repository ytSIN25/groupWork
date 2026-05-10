<?php
require_once 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index_login.php');
    exit();
}

$message = "";
$mode = "add";
$target_promo = null;

// Check if in EDIT mode
if (isset($_GET['id'])) {
    $promo_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM promotions WHERE promotion_id = ?");
    $stmt->bind_param("i", $promo_id);
    $stmt->execute();
    $target_promo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($target_promo) {
        $mode = "edit";
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $promo_code     = strtoupper(trim($_POST['promo_code']));
    $discount_value = $_POST['discount_value'] ?? 0.00;
    $description    = $_POST['description'] ?? "";
    $min_spend      = $_POST['minimum_spend'] ?? 0.00;
    $user_id        = $_SESSION['user_id'];

    if ($mode === 'add') {
        $stmt = $conn->prepare("INSERT INTO promotions (user_id, promo_code, discount_value, description, minimum_spend, is_active) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("isdsd", $user_id, $promo_code, $discount_value, $description, $min_spend);
        if ($stmt->execute()) {
            $message = "Promotion minted successfully!";
        } else {
            $message = "Error: Code might already exist.";
        }
        $stmt->close();
    } else {
        // UPDATE
        $stmt = $conn->prepare("UPDATE promotions SET promo_code=?, discount_value=?, description=?, minimum_spend=? WHERE promotion_id=?");
        $stmt->bind_param("sdsdi", $promo_code, $discount_value, $description, $min_spend, $_GET['id']);
        if ($stmt->execute()) {
            $message = "Promotion updated successfully!";
            $target_promo['promo_code'] = $promo_code;
            $target_promo['discount_value'] = $discount_value;
            $target_promo['description'] = $description;
            $target_promo['minimum_spend'] = $min_spend;
        } else {
            $message = "Error updating promotion.";
        }
        $stmt->close();
    }
}

// Handle Status Toggle
if (isset($_GET['toggle_id'])) {
    $tid = $_GET['toggle_id'];
    $conn->query("UPDATE promotions SET is_active = 1 - is_active WHERE promotion_id = $tid");
    header("Location: admin_set_promotion.php?id=$tid");
    exit();
}

// Deletion
if (isset($_GET['delete_id'])) {
    $del_id = $_GET['delete_id'];
    $conn->query("DELETE FROM promotions WHERE promotion_id = $del_id");
    header('Location: dashboard_admin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUMIÈRE — <?php echo $mode === 'add' ? 'Issue' : 'Refine'; ?> Promotion</title>
    <link rel="stylesheet" href="css/base.css?v=5">
    <link rel="stylesheet" href="css/pages/dashboard.css?v=5">
    <link rel="stylesheet" href="css/pages/footer.css?v=5">
    <link rel="stylesheet" href="css/global.css?v=5">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <style>
        .edit-controls {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px dashed rgba(0,0,0,0.1);
        }
        
        .btn-status-toggle {
            flex: 1;
            padding: 12px;
            border-radius: 4px;
            text-align: center;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
            font-size: 0.9rem;
        }

        .btn-active { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .btn-active:hover { background: #c8e6c9; }
        
        .btn-paused { background: #fff3e0; color: #ef6c00; border: 1px solid #ffe0b2; }
        .btn-paused:hover { background: #ffe0b2; }

        .btn-delete {
            flex: 1;
            padding: 12px;
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
            border-radius: 4px;
            text-align: center;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
            font-size: 0.9rem;
        }
        .btn-delete:hover { background: #ffcdd2; }
    </style>
</head>

<body>
    <div class="film-grain"></div>
    <div class="page-transition active" id="pageTransition">
        <span class="trans-logo">LUMIÈRE</span>
    </div>

    <nav class="lumiere-nav" style="padding: 15px 5%;">
        <a href="dashboard_admin.php" class="lumiere-logo" style="gap: 10px;">
            <img src="assets/images/logo.svg?v=5" alt="LUMIÈRE" style="height: 40px;">
            <span style="font-family: var(--font-accent); font-size: 1rem; color: var(--mocha); letter-spacing: 0.2em;">STAFF</span>
        </a>
        <div class="nav-links">
            <a href="dashboard_admin.php" class="nav-link">Dashboard</a>
            <a href="admin_add_movie.php" class="nav-link">Catalog</a>
            <a href="admin_set_promotion.php" class="nav-link" style="color: var(--sunset-coral);">Promotions</a>
            <a href="movies.php" class="nav-link" style="color: var(--sunset-rose);">Exit Staff</a>
        </div>
    </nav>

    <div class="page-wrapper org-wrapper" style="padding: 120px 5% 60px;">
        <div style="max-width: 800px; margin: 0 auto;">
            <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom: 30px;">
                <div>
                    <h1 class="fade-up" style="margin-bottom: 8px;">
                        <?php echo $mode === 'add' ? 'Issue Promotion' : 'Refine Promotion'; ?>
                    </h1>
                    <p style="color: var(--mocha); font-style: italic; font-family: var(--font-accent);" class="fade-up">
                        <?php echo $mode === 'add' ? 'Generate new coupons for patron redemption.' : 'Modifying the ledger entry for ' . htmlspecialchars($target_promo['promo_code']); ?>
                    </p>
                </div>

                <?php if ($mode === 'edit'): ?>
                    <a href="admin_set_promotion.php" class="btn-primary" style="padding: 8px 20px; font-size: 0.8rem;">+ New Coupon</a>
                <?php endif; ?>
            </div>

            <div style="background: var(--cream); color: var(--bg-deep); padding: 40px; border-radius: 8px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);" class="scale-in">
                <form action="" method="POST">
                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex: 1;">
                            <label style="color: var(--bg-deep); font-weight: 600;">Promo Code</label>
                            <input type="text" name="promo_code" class="typewriter-input" 
                                style="color: var(--bg-deep); text-transform: uppercase;" 
                                placeholder="e.g. GOLD20" required
                                value="<?php echo $target_promo ? htmlspecialchars($target_promo['promo_code']) : ''; ?>">
                        </div>
                        
                        <div class="form-group" style="flex: 1;">
                            <label style="color: var(--bg-deep); font-weight: 600;">Discount Value (€)</label>
                            <input type="number" step="0.01" name="discount_value" class="typewriter-input" 
                                style="color: var(--bg-deep);" placeholder="5.00" required
                                value="<?php echo $target_promo ? $target_promo['discount_value'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label style="color: var(--bg-deep); font-weight: 600;">Description</label>
                        <input type="text" name="description" class="typewriter-input" 
                            style="color: var(--bg-deep);" placeholder="e.g. Student Appreciation Discount"
                            value="<?php echo $target_promo ? htmlspecialchars($target_promo['description']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label style="color: var(--bg-deep); font-weight: 600;">Minimum Spend (€)</label>
                        <input type="number" step="0.01" name="minimum_spend" class="typewriter-input" 
                            style="color: var(--bg-deep);" placeholder="0.00"
                            value="<?php echo $target_promo ? $target_promo['minimum_spend'] : ''; ?>">
                    </div>

                    <button class="btn-primary" name="submit_promo" style="width: 100%; margin-top: 15px; color: var(--bg-deep); border-color: var(--bg-deep);" type="submit">
                        <?php echo $mode === 'add' ? 'Mint Coupon' : 'Update Ledger'; ?>
                    </button>
                    
                    <?php if ($mode === 'edit'): ?>
                        <div class="edit-controls">
                            <?php if ($target_promo['is_active']): ?>
                                <a href="?toggle_id=<?php echo $target_promo['promotion_id']; ?>" class="btn-status-toggle btn-paused">
                                    ⏸ Pause Coupon
                                </a>
                            <?php else: ?>
                                <a href="?toggle_id=<?php echo $target_promo['promotion_id']; ?>" class="btn-status-toggle btn-active">
                                    ▶ Resume Coupon
                                </a>
                            <?php endif; ?>
                            
                            <a href="?delete_id=<?php echo $target_promo['promotion_id']; ?>" class="btn-delete" 
                               onclick="return confirm('Permanently retire this coupon from the archives?')">
                                ✕ Retire Coupon
                            </a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/main.js?v=5"></script>

    <?php if ($message !== ""): ?>
    <script>
        Swal.fire({
            title: '<?php echo strpos($message, 'Error') === false ? 'Success' : 'Notice'; ?>',
            text: '<?php echo $message; ?>',
            icon: '<?php echo strpos($message, 'Error') === false ? 'success' : 'warning'; ?>',
            background: '#F2E8D5',
            color: '#0D0B0E',
            confirmButtonColor: '#2A7A7A'
        });
    </script>
    <?php endif; ?>
</body>

</html>