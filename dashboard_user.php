<?php
require_once 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index_login.php');
    exit;
}

// Fetch current user
$stmt = $conn->prepare('SELECT user_id, name, email, role, tier, avatar, created_at FROM users WHERE user_id = ?');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    header('Location: index_login.php');
    exit;
}

// Redirect admin to their own dashboard
if ($user['role'] === 'admin') {
    header('Location: dashboard_admin.php');
    exit;
}

$userId = $user['user_id'];
$firstName = explode(' ', $user['name'])[0];

// ---------- Stats ----------
// Total orders
$stmt = $conn->prepare('SELECT COUNT(*) as total FROM orders WHERE user_id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$totalOrders = (int) $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Total spent
$stmt = $conn->prepare('SELECT IFNULL(SUM(total_price), 0) as total FROM orders WHERE user_id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$totalSpent = (float) $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Total ratings given
$stmt = $conn->prepare('SELECT COUNT(*) as total FROM ratings WHERE user_id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$totalRatings = (int) $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// ---------- Upcoming tickets ----------
$stmt = $conn->prepare(
    'SELECT o.order_id, o.seats, o.num_seats, o.total_price, o.order_date,
            o.show_date, o.show_time,
            m.movie_name, m.poster_path
     FROM orders o
     JOIN movies m ON o.movie_id = m.movie_id
     WHERE o.user_id = ?
       AND CONCAT(o.show_date, " ", o.show_time) >= NOW()
     ORDER BY o.show_date ASC, o.show_time ASC'
);
$stmt->bind_param('i', $userId);
$stmt->execute();
$upcomingTickets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------- Past orders ----------
$stmt = $conn->prepare(
    'SELECT o.order_id, o.seats, o.num_seats, o.total_price, o.order_date,
            o.show_date, o.show_time,
            m.movie_name
     FROM orders o
     JOIN movies m ON o.movie_id = m.movie_id
     WHERE o.user_id = ?
       AND CONCAT(o.show_date, " ", o.show_time) < NOW()
     ORDER BY o.show_date DESC
     LIMIT 20'
);
$stmt->bind_param('i', $userId);
$stmt->execute();
$pastOrders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------- Ratings / History ----------
$stmt = $conn->prepare(
    'SELECT r.content, r.star_num, r.rating_id,
            m.movie_name
     FROM ratings r
     JOIN movies m ON r.movie_id = m.movie_id
     WHERE r.user_id = ?
     ORDER BY r.rating_id DESC
     LIMIT 20'
);
$stmt->bind_param('i', $userId);
$stmt->execute();
$myRatings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------- User preferences ----------
$stmt = $conn->prepare('SELECT preferred_seating, preferred_snack, preferred_genre FROM user_preferences WHERE user_id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$preferences = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$preferences) {
    $preferences = [
        'preferred_seating' => 'The Circle (Balcony)',
        'preferred_snack'   => '',
        'preferred_genre'   => '',
    ];
}

// ---------- Available promotions ----------
$stmt = $conn->prepare(
    'SELECT promotion_id, discount_value, promo_code, description, minimum_spend
     FROM promotions
     WHERE is_active = 1
     ORDER BY promotion_id DESC'
);
$stmt->execute();
$promotions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUMIÈRE - Patron Dashboard</title>
    <link rel="stylesheet" href="css/base.css?v=5">
    <link rel="stylesheet" href="css/pages/dashboard.css?v=5">
    <link rel="stylesheet" href="css/pages/footer.css?v=5">
    <link rel="stylesheet" href="css/global.css?v=5">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .sidebar {
            width: 280px;
            flex-shrink: 0;
            position: relative;
            background: rgba(26, 21, 32, 0.5);
            padding: 40px 20px;
            border-radius: 12px;
            border: 1px solid rgba(212, 168, 83, 0.1);
        }

        .tab-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            color: var(--mocha);
            font-family: var(--font-accent);
            font-size: 1.1rem;
            padding: 18px 15px;
            cursor: pointer;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.4s var(--ease-smooth);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .tab-btn:hover {
            background: rgba(212, 168, 83, 0.05);
            color: var(--cream);
            transform: translateX(8px);
        }

        .tab-btn.active {
            background: linear-gradient(90deg, rgba(232, 115, 90, 0.15) 0%, transparent 100%);
            color: var(--sunset-coral);
            border-left: 3px solid var(--sunset-coral);
        }

        .ticket-card {
            background: var(--cream);
            color: var(--bg-deep);
            border-radius: 12px;
            padding: 0;
            margin-bottom: 35px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            position: relative;
            transition: transform 0.4s var(--ease-smooth);
        }

        .ticket-card:hover {
            transform: translateY(-5px) scale(1.01);
        }

        .ticket-main {
            flex: 3;
            padding: 30px;
            border-right: 2px dashed rgba(13, 11, 14, 0.2);
            position: relative;
        }

        .ticket-stub {
            flex: 1;
            padding: 30px;
            background: rgba(212, 168, 83, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .ticket-card::before,
        .ticket-card::after {
            content: '';
            position: absolute;
            right: calc(25% - 15px);
            width: 30px;
            height: 30px;
            background: var(--bg-deep);
            border-radius: 50%;
            z-index: 2;
        }

        .ticket-card::before { top: -15px; }
        .ticket-card::after { bottom: -15px; }

        .ticket-title {
            font-family: var(--font-display);
            font-size: 2.2rem;
            font-style: italic;
            margin: 10px 0;
            color: var(--bg-deep);
        }

        .ticket-meta {
            font-family: var(--font-accent);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-size: 0.9rem;
            color: var(--mocha);
        }

        .user-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .user-stat-box {
            background: var(--bg-card);
            border: 1px solid rgba(212, 168, 83, 0.1);
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            transition: var(--transition);
        }

        .user-stat-box:hover {
            border-color: var(--sunset-coral);
            transform: translateY(-5px);
        }

        .user-stat-value {
            font-family: var(--font-display);
            font-size: 2.5rem;
            color: var(--gold);
            display: block;
        }

        .user-stat-label {
            font-family: var(--font-accent);
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.1em;
            color: var(--mocha);
        }

        .tab-pane {
            display: none;
            animation: fadeInBlur 0.8s var(--ease-smooth) forwards;
        }

        .tab-pane.active {
            display: block;
        }

        @keyframes fadeInBlur {
            from { opacity: 0; filter: blur(10px); transform: translateY(20px); }
            to   { opacity: 1; filter: blur(0);   transform: translateY(0); }
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            opacity: 0.6;
        }
        .empty-state p {
            font-family: var(--font-accent);
            color: var(--mocha);
            font-style: italic;
            font-size: 1.1rem;
        }

        .order-row {
            background: var(--bg-card);
            border: 1px solid rgba(212, 168, 83, 0.1);
            padding: 20px 25px;
            border-radius: 10px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .star-display { color: var(--gold); letter-spacing: 2px; }
    </style>
</head>

<body>

    <div class="film-grain"></div>
    <div class="page-transition active" id="pageTransition"><span class="trans-logo">LUMIÈRE</span></div>

    <nav class="lumiere-nav">
        <div class="nav-left" style="display:flex; align-items:center; gap:25px;">
            <a href="index.php" class="lumiere-logo"><img src="assets/images/logo.svg?v=5" alt="LUMIÈRE" style="height:45px;"></a>
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Home</a>
            <a href="movies.php" class="nav-link">Now Showing</a>
            <a href="history.php" class="nav-link">My Tickets</a>
            <a href="dashboard_user.php" class="nav-link" style="color:var(--sunset-coral);">Account</a>
            <a href="about.php" class="nav-link">The Cinema</a>
        </div>
    </nav>

    <div class="page-wrapper" style="padding-top:140px; max-width:1400px; margin:0 auto; display:flex; gap:60px;">

        <!-- ---------- Sidebar ---------- -->
        <aside class="sidebar fade-up">
            <div style="text-align:center; margin-bottom:40px;">
                <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Profile"
                    style="width:100px; height:100px; border-radius:50%; border:3px solid var(--gold); margin-bottom:15px; padding:5px; background:var(--bg-card);">
                <h2 style="font-family:var(--font-display); font-style:italic; font-size:1.8rem; color:var(--cream);">
                    <?= htmlspecialchars($user['name']) ?>
                </h2>
                <p style="font-family:var(--font-accent); color:var(--gold); text-transform:uppercase; font-size:0.8rem; letter-spacing:0.2em;">
                    <?= htmlspecialchars($user['tier']) ?>
                </p>
            </div>

            <button class="tab-btn active" onclick="switchTab(event, 'overview')"><span>Dashboard</span> ✦</button>
            <button class="tab-btn" onclick="switchTab(event, 'tickets')"><span>My Tickets</span> 🎟️</button>
            <button class="tab-btn" onclick="switchTab(event, 'vouchers')"><span>Vouchers</span> 🏷️</button>
            <button class="tab-btn" onclick="switchTab(event, 'history')"><span>Cinematic History</span> 🎞️</button>
            <button class="tab-btn" onclick="switchTab(event, 'preferences')"><span>Preferences</span> 🍸</button>
            <button class="tab-btn" onclick="switchTab(event, 'settings')"><span>Profile Settings</span> ⚙️</button>

            <div class="divider" style="margin:30px 0;"></div>
            <button onclick="logout()" class="btn-primary" style="width:100%; text-align:center; border-color:var(--retro-red); color:var(--retro-red); cursor:pointer;">Log Out</button>
        </aside>

        <!-- ---------- Main content ---------- -->
        <main style="flex:1;">

            <!-- ----- Overview ----- -->
            <div id="overview" class="tab-pane active">
                <div class="section-header fade-up" style="text-align:left;">
                    <h1 style="font-size:3rem; font-style:italic;">Welcome Back, <?= htmlspecialchars($firstName) ?></h1>
                    <p style="margin:0;">Your portal to the golden age of cinema.</p>
                </div>

                <div class="user-stats fade-up" data-delay="200">
                    <div class="user-stat-box">
                        <span class="user-stat-value"><?= $totalOrders ?></span>
                        <span class="user-stat-label">Films This Year</span>
                    </div>
                    <div class="user-stat-box">
                        <span class="user-stat-value"><?= number_format($totalSpent * 100, 0) ?></span>
                        <span class="user-stat-label">Membership Points</span>
                    </div>
                    <div class="user-stat-box">
                        <?php
                            $tierLower = strtolower($user['tier']);
                            if (strpos($tierLower, 'bronze') !== false) {
                                $tierLabel = 'Bronze';
                            } elseif (strpos($tierLower, 'silver') !== false) {
                                $tierLabel = 'Silver';
                            } else {
                                $tierLabel = 'Gold';
                            }
                        ?>
                        <span class="user-stat-value"><?= $tierLabel ?></span>
                        <span class="user-stat-label">Tier Progress</span>
                    </div>
                </div>

                <h2 class="fade-up" style="margin-bottom:25px;">Up Next on the Silver Screen</h2>

                <?php if (empty($upcomingTickets)): ?>
                    <div class="empty-state fade-up" data-delay="300">
                        <p>No upcoming screenings booked yet.</p>
                        <a href="movies.php" class="btn-coral" style="display:inline-block; margin-top:15px; text-decoration:none;">Browse Movies</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($upcomingTickets as $i => $ticket): ?>
                        <div class="ticket-card fade-up" data-delay="<?= 300 + ($i * 100) ?>">
                            <div class="ticket-main">
                                <span class="ticket-meta">LUMIÈRE PREMIUM ADMISSION</span>
                                <h3 class="ticket-title"><?= htmlspecialchars($ticket['movie_name']) ?></h3>
                                <p style="font-family:var(--font-accent); font-size:1.1rem; color:var(--bg-deep); opacity:0.7;">
                                    <?= date('M j, Y', strtotime($ticket['show_date'])) ?> •
                                    <?= date('H:i', strtotime($ticket['show_time'])) ?>
                                </p>
                                <div style="margin-top:20px; display:flex; gap:15px; flex-wrap:wrap;">
                                    <?php foreach (explode(',', $ticket['seats']) as $seat): ?>
                                        <span style="background:var(--bg-deep); color:var(--cream); padding:5px 15px; border-radius:4px; font-family:var(--font-accent);">
                                            <?= htmlspecialchars(trim($seat)) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="ticket-stub">
                                <div class="qr-placeholder" style="width:80px; height:80px; margin-bottom:15px;"></div>
                                <span style="font-family:'Courier New', monospace; font-size:0.8rem; font-weight:bold;">#LX-<?= str_pad($ticket['order_id'], 5, '0', STR_PAD_LEFT) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- ----- My tickets ----- -->
            <div id="tickets" class="tab-pane">
                <h1 style="font-style:italic; margin-bottom:40px;">Your Active Admissions</h1>

                <?php if (empty($upcomingTickets)): ?>
                    <div class="empty-state fade-up" data-delay="300">
                        <p>You have no active tickets.</p>
                        <a href="movies.php" class="btn-coral" style="display:inline-block; margin-top:15px; text-decoration:none;">Browse Movies</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($upcomingTickets as $ticket): ?>
                        <div class="ticket-card">
                            <div class="ticket-main">
                                <span class="ticket-meta">LUMIÈRE PREMIUM ADMISSION</span>
                                <h3 class="ticket-title"><?= htmlspecialchars($ticket['movie_name']) ?></h3>
                                <p style="font-family:var(--font-accent); color:var(--bg-deep); opacity:0.7;">
                                    <?= date('M j, Y', strtotime($ticket['show_date'])) ?> •
                                    <?= date('H:i', strtotime($ticket['show_time'])) ?>
                                </p>
                                <div style="margin-top:15px; display:flex; gap:10px; flex-wrap:wrap;">
                                    <?php foreach (explode(',', $ticket['seats']) as $seat): ?>
                                        <span style="background:var(--bg-deep); color:var(--cream); padding:5px 15px; border-radius:4px; font-family:var(--font-accent);">
                                            <?= htmlspecialchars(trim($seat)) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="ticket-stub">
                                <div class="qr-placeholder" style="width:60px; height:60px; margin-bottom:10px;"></div>
                                <span style="font-family:'Courier New', monospace; font-size:0.75rem; font-weight:bold;">#LX-<?= str_pad($ticket['order_id'], 5, '0', STR_PAD_LEFT) ?></span>
                                <span style="font-family:var(--font-accent); font-size:0.8rem; margin-top:5px; color:var(--gold);">
                                    $<?= number_format($ticket['total_price'], 2) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- ----- Vouchers ----- -->
            <div id="vouchers" class="tab-pane">
                <h1 style="font-style:italic; margin-bottom:40px;">Your Vouchers</h1>

                <?php if (empty($promotions)): ?>
                    <div class="empty-state">
                        <p>No vouchers available right now.</p>
                    </div>
                <?php else: ?>
                    <div class="voucher-grid">
                        <?php foreach ($promotions as $promo): ?>
                            <div class="coupon-card">
                                <h3 style="color:var(--gold); font-size:1.6rem; margin-bottom:10px;">
                                    <?= $promo['discount_value'] ?>% Off
                                </h3>
                                <p style="color:var(--mocha); font-style:italic; margin-bottom:20px;">
                                    <?= htmlspecialchars($promo['description'] ?? 'No description') ?>
                                </p>
                                <?php if ($promo['minimum_spend'] > 0): ?>
                                    <p style="color:var(--mocha); font-size:0.85rem; margin-bottom:10px;">
                                        Minimum spend: $<?= number_format($promo['minimum_spend'], 2) ?>
                                    </p>
                                <?php endif; ?>
                                <div style="font-family:monospace; font-size:1.1rem; border-top:1px dashed rgba(212,168,83,0.3); padding-top:12px; letter-spacing:0.15em;">
                                    <?= htmlspecialchars($promo['promo_code']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ----- Cinematic history ----- -->
            <div id="history" class="tab-pane">
                <h1 style="font-style:italic; margin-bottom:40px;">Cinematic History</h1>

                <?php if (empty($pastOrders) && empty($myRatings)): ?>
                    <div class="empty-state">
                        <p>Your cinematic journey has yet to begin.</p>
                    </div>
                <?php else: ?>

                    <?php if (!empty($myRatings)): ?>
                        <h3 style="margin-bottom:20px;">Your Reviews</h3>
                        <div class="reviews-grid" style="margin-bottom:50px;">
                            <?php foreach ($myRatings as $rating): ?>
                                <div class="review-card">
                                    <div class="star-display">
                                        <?= str_repeat('★', (int)$rating['star_num']) . str_repeat('☆', 5 - (int)$rating['star_num']) ?>
                                    </div>
                                    <h3><?= htmlspecialchars($rating['movie_name']) ?></h3>
                                    <?php if ($rating['content']): ?>
                                        <p class="review-text">"<?= htmlspecialchars($rating['content']) ?>"</p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($pastOrders)): ?>
                        <h3 style="margin-bottom:20px;">Past Visits</h3>
                        <?php foreach ($pastOrders as $order): ?>
                            <div class="order-row">
                                <div>
                                    <strong style="font-family:var(--font-display); font-style:italic; font-size:1.2rem; color:var(--cream);">
                                        <?= htmlspecialchars($order['movie_name']) ?>
                                    </strong>
                                    <p style="font-family:var(--font-accent); font-size:0.85rem; color:var(--mocha); margin-top:5px;">
                                        <?= date('M j, Y', strtotime($order['show_date'])) ?> •
                                        <?= date('H:i', strtotime($order['show_time'])) ?> •
                                        Seats: <?= htmlspecialchars($order['seats']) ?>
                                    </p>
                                </div>
                                <span style="font-family:var(--font-accent); color:var(--gold); font-size:1.1rem;">
                                    $<?= number_format($order['total_price'], 2) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                <?php endif; ?>
            </div>

            <!-- ----- Preferences ----- -->
            <div id="preferences" class="tab-pane">
                <h1 style="font-style:italic; margin-bottom:40px;">Patron Preferences</h1>
                <div class="vintage-card">
                    <div class="corner-dec corner-tl"></div>
                    <div class="corner-dec corner-tr"></div>
                    <div class="corner-dec corner-bl"></div>
                    <div class="corner-dec corner-br"></div>

                    <div class="form-group">
                        <label>Preferred Seating Area</label>
                        <select id="prefSeating">
                            <option value="The Stalls (Front)" <?= $preferences['preferred_seating'] === 'The Stalls (Front)' ? 'selected' : '' ?>>The Stalls (Front)</option>
                            <option value="The Circle (Balcony)" <?= $preferences['preferred_seating'] === 'The Circle (Balcony)' ? 'selected' : '' ?>>The Circle (Balcony)</option>
                            <option value="Royal Box" <?= $preferences['preferred_seating'] === 'Royal Box' ? 'selected' : '' ?>>Royal Box</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Favorite Lobby Snack</label>
                        <input type="text" id="prefSnack" value="<?= htmlspecialchars($preferences['preferred_snack']) ?>" placeholder="e.g. Velvet Chocolates, Classic Popcorn...">
                    </div>

                    <div class="form-group">
                        <label>Preferred Genre</label>
                        <select id="prefGenre">
                            <option value="" <?= $preferences['preferred_genre'] === '' ? 'selected' : '' ?>>No Preference</option>
                            <option value="Action" <?= $preferences['preferred_genre'] === 'Action' ? 'selected' : '' ?>>Action</option>
                            <option value="Adventure" <?= $preferences['preferred_genre'] === 'Adventure' ? 'selected' : '' ?>>Adventure</option>
                            <option value="Comedy" <?= $preferences['preferred_genre'] === 'Comedy' ? 'selected' : '' ?>>Comedy</option>
                            <option value="Crime" <?= $preferences['preferred_genre'] === 'Crime' ? 'selected' : '' ?>>Crime</option>
                            <option value="Drama" <?= $preferences['preferred_genre'] === 'Drama' ? 'selected' : '' ?>>Drama</option>
                            <option value="Fantasy" <?= $preferences['preferred_genre'] === 'Fantasy' ? 'selected' : '' ?>>Fantasy</option>
                            <option value="Historical" <?= $preferences['preferred_genre'] === 'Historical' ? 'selected' : '' ?>>Historical</option>
                            <option value="Horror" <?= $preferences['preferred_genre'] === 'Horror' ? 'selected' : '' ?>>Horror</option>
                            <option value="Musical" <?= $preferences['preferred_genre'] === 'Musical' ? 'selected' : '' ?>>Musical</option>
                            <option value="Romance" <?= $preferences['preferred_genre'] === 'Romance' ? 'selected' : '' ?>>Romance</option>
                            <option value="Sci-Fi" <?= $preferences['preferred_genre'] === 'Sci-Fi' ? 'selected' : '' ?>>Sci-Fi</option>
                            <option value="Thriller" <?= $preferences['preferred_genre'] === 'Thriller' ? 'selected' : '' ?>>Thriller</option>
                        </select>
                    </div>

                    <button class="btn-coral" style="margin-top:20px;" onclick="savePreferences()">Save Aesthetics</button>
                </div>
            </div>

            <!-- ----- Profile settings ----- -->
            <div id="settings" class="tab-pane">
                <h1 style="font-style:italic; margin-bottom:40px;">Profile Settings</h1>
                <div class="vintage-card">
                    <div class="corner-dec corner-tl"></div>
                    <div class="corner-dec corner-tr"></div>
                    <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" id="settingsName" value="<?= htmlspecialchars($user['name']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <input type="text" value="<?= ucfirst($user['role']) ?>" disabled style="opacity:0.5;">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Email</label>
                            <input type="email" id="settingsEmail" value="<?= htmlspecialchars($user['email']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Tier</label>
                            <input type="text" value="<?= htmlspecialchars($user['tier']) ?>" disabled style="opacity:0.5;">
                        </div>
                        <div class="form-group">
                            <label>Member Since</label>
                            <input type="text" value="<?= date('M j, Y', strtotime($user['created_at'])) ?>" disabled style="opacity:0.5;">
                        </div>
                    </div>
                    <button class="btn-coral" style="margin-top:20px;" onclick="updateProfile()">Update Profile</button>
                </div>
            </div>

        </main>
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

    <script src="js/main.js?v=5"></script>
    <script>
        function switchTab(event, tabId) {
            document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        async function logout() {
            try {
                await fetch('api_logout.php', { method: 'POST' });
            } catch (e) {}
            window.location.href = 'index_login.php';
        }

        async function savePreferences() {
            const seating = document.getElementById('prefSeating').value;
            const snack   = document.getElementById('prefSnack').value.trim();
            const genre   = document.getElementById('prefGenre').value;

            try {
                const resp = await fetch('api_update_preferences.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ seating, snack, genre })
                });
                const data = await resp.json();

                if (data.success) {
                    Swal.fire({ title: 'Saved', text: 'Your preferences have been updated.', icon: 'success', background: '#1A1520', color: '#F2E8D5', confirmButtonColor: '#E8735A' });
                } else {
                    Swal.fire({ title: 'Error', text: data.message, icon: 'error', background: '#1A1520', color: '#F2E8D5', confirmButtonColor: '#E8735A' });
                }
            } catch (err) {
                Swal.fire({ title: 'Error', text: 'Something went wrong.', icon: 'warning' });
            }
        }

        async function updateProfile() {
            const name  = document.getElementById('settingsName').value.trim();
            const email = document.getElementById('settingsEmail').value.trim();

            if (!name || !email) {
                Swal.fire({ title: 'Error', text: 'Name and email are required.', icon: 'warning', background: '#1A1520', color: '#F2E8D5', confirmButtonColor: '#E8735A' });
                return;
            }

            try {
                const resp = await fetch('api_update_profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, email })
                });
                const data = await resp.json();

                if (data.success) {
                    Swal.fire({ title: 'Updated', text: 'Profile saved.', icon: 'success', background: '#1A1520', color: '#F2E8D5', confirmButtonColor: '#E8735A' })
                        .then(() => location.reload());
                } else {
                    Swal.fire({ title: 'Error', text: data.message, icon: 'error', background: '#1A1520', color: '#F2E8D5', confirmButtonColor: '#E8735A' });
                }
            } catch (err) {
                Swal.fire({ title: 'Error', text: 'Something went wrong.', icon: 'warning' });
            }
        }
    </script>
</body>

</html>
