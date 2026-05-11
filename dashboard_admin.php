<?php
require_once 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index_login.php?force=1');
    exit();
}

// Fetch movies owned by this admin
$admin_id = $_SESSION['user_id'];

// Fetch admin details
$user_query = "SELECT name, tier, avatar FROM users WHERE user_id = $admin_id";
$user_result = $conn->query($user_query);
$user_data = $user_result->fetch_assoc();
$admin_name = $user_data['name'] ?? 'Admin';
$admin_tier = $user_data['tier'] ?? 'Staff';
$admin_avatar = $user_data['avatar'] ?? "https://api.dicebear.com/7.x/notionists/svg?seed=" . urlencode($admin_name);

$movies_query = "SELECT * FROM movies WHERE user_id = $admin_id ORDER BY movie_id DESC";
$movies_result = $conn->query($movies_query);
$movies = [];
if ($movies_result) {
    while ($row = $movies_result->fetch_assoc()) {
        $movies[] = $row;
    }
}

// Fetch promotions
$promos_result = $conn->query("SELECT * FROM promotions WHERE user_id = $admin_id ORDER BY promotion_id DESC");
$promos = [];
if ($promos_result) {
    while ($row = $promos_result->fetch_assoc()) {
        $promos[] = $row;
    }
}

// --- DYNAMIC REVENUE CALCULATION ---
// Calculate daily revenue for the current week
$weekly_revenue = array_fill(0, 7, 0);
$rev_query = "
    SELECT 
        WEEKDAY(o.order_date) as day_index,
        SUM(o.total_price) as daily_total
    FROM orders o
    JOIN movies m ON o.movie_id = m.movie_id
    WHERE m.user_id = $admin_id
    AND YEARWEEK(o.order_date, 1) = YEARWEEK(CURDATE(), 1)
    GROUP BY day_index
";
$rev_result = $conn->query($rev_query);
if ($rev_result) {
    while ($row = $rev_result->fetch_assoc()) {
        $weekly_revenue[(int)$row['day_index']] = (float)$row['daily_total'];
    }
}

$max_rev = max($weekly_revenue) ?: 1;
$total_week_revenue = array_sum($weekly_revenue);

// --- REVENUE BY MOVIE BREAKDOWN ---
$movie_labels = [];
$movie_revenue = [];
$movie_rev_query = "
    SELECT 
        m.movie_name,
        SUM(o.total_price) as movie_total
    FROM orders o
    JOIN movies m ON o.movie_id = m.movie_id
    WHERE m.user_id = $admin_id
    GROUP BY m.movie_id
";

$movie_rev_result = $conn->query($movie_rev_query);
if ($movie_rev_result) {
    while ($row = $movie_rev_result->fetch_assoc()) {
        $movie_labels[] = $row['movie_name'];
        $movie_revenue[] = (float)$row['movie_total'];
    }
}

// --- ALL-TIME STATISTICS ---
$all_time_rev_query = "
    SELECT 
        SUM(o.total_price) as total_gross,
        SUM(o.num_seats) as total_admissions
    FROM orders o
    JOIN movies m ON o.movie_id = m.movie_id
    WHERE m.user_id = $admin_id
";
$all_time_res = $conn->query($all_time_rev_query);
$all_time_data = $all_time_res->fetch_assoc();
$total_gross_all_time = (float)($all_time_data['total_gross'] ?? 0);
$total_admissions_all_time = (int)($all_time_data['total_admissions'] ?? 0);

// --- ACTIVE CATALOG SIZE ---
$catalog_query = "
    SELECT COUNT(*) as movie_count 
    FROM movies 
    WHERE user_id = $admin_id 
      AND (start_date IS NOT NULL AND start_date != '0000-00-00')
      AND CURDATE() >= start_date 
      AND CURDATE() <= DATE_ADD(start_date, INTERVAL 14 DAY)
";
$catalog_res = $conn->query($catalog_query);
$catalog_data = $catalog_res->fetch_assoc();
$active_catalog_size = (int)($catalog_data['movie_count'] ?? 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUMIÈRE - Director's Dashboard</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/pages/dashboard.css">
    <link rel="stylesheet" href="css/pages/footer.css">
    <link rel="stylesheet" href="css/global.css">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .admin-sidebar {
            width: 300px;
            background: linear-gradient(180deg, var(--noir, #0a0a0a), var(--bg-deep, #120e15));
            border-right: 1px solid var(--retro-red-glow, rgba(178, 34, 34, 0.5));
            padding: 40px 25px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            font-family: var(--font-accent);
        }

        .admin-sidebar h2 {
            font-size: 1.2rem;
            color: var(--mocha, #8b7355);
            text-transform: uppercase;
            letter-spacing: 0.2em;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(212, 168, 83, 0.1);
            padding-bottom: 10px;
        }

        .admin-nav-btn {
            background: none;
            border: none;
            color: rgb(154, 139, 122);
            font-family: inherit;
            text-align: left;
            padding: 15px;
            font-size: 1rem;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .admin-nav-btn:hover {
            background: rgba(178, 34, 34, 0.1);
            color: var(--retro-red, #b22222);
            padding-left: 20px;
        }

        .admin-nav-btn.active {
            background: var(--retro-red, #b22222);
            color: rgb(252, 190, 190);
            box-shadow: 0 0 20px var(--retro-red-glow, rgba(178, 34, 34, 0.5));
        }

        .stat-card-premium {
            background: var(--bg-card, #1a1520);
            border: 1px solid rgba(212, 168, 83, 0.15);
            padding: 30px;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        .stat-card-premium:hover {
            border-color: var(--retro-red, #b22222);
            transform: translateY(-5px);
        }

        .stat-card-premium::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--retro-red, #b22222);
            opacity: 0.3;
        }

        .stat-value-admin {
            font-family: var(--font-display, serif);
            font-size: 3rem;
            color: var(--gold, #d4a853);
            line-height: 1;
        }

        .stat-label-admin {
            font-family: var(--font-accent, sans-serif);
            color: var(--mocha, #8b7355);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-size: 0.9rem;
        }

        .performance-bar-container {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 3px;
            margin-top: 15px;
            overflow: hidden;
        }

        .performance-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--retro-red, #b22222), var(--sunset-coral, #e8735a));
            width: 0;
            transition: width 1.5s ease-in-out;
        }

        /* --- BAR CHART FIXES ADDED HERE --- */
        .bar-chart {
            display: flex;
            align-items: flex-end;
            /* Anchors bars to the bottom */
            justify-content: space-around;
            border-bottom: 2px solid rgba(212, 168, 83, 0.2);
            padding-bottom: 0;
            margin-bottom: 10px;
            gap: 15px;
        }

        .bar {
            width: 45px;
            /* Gives the bars a consistent width */
            border-radius: 4px 4px 0 0;
            position: relative;
            transition: transform 0.3s ease, filter 0.3s ease;
            cursor: pointer;
            transform-origin: bottom;
        }

        .bar:hover {
            transform: scaleY(1.05);
            /* Slight pop-up effect */
            filter: brightness(1.2);
        }

        /* Tooltip to show the value on hover */
        .bar::after {
            content: attr(data-value);
            position: absolute;
            top: -35px;
            left: 50%;
            transform: translateX(-50%);
            font-family: var(--font-accent, sans-serif);
            font-size: 0.8rem;
            color: var(--cream, #fff);
            background: var(--bg-deep, #111);
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid var(--gold, #d4a853);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            white-space: nowrap;
        }

        .bar:hover::after {
            opacity: 1;
        }

        .admin-table-premium {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        .admin-table-premium th {
            text-align: left;
            padding: 10px 20px;
            font-family: var(--font-accent, sans-serif);
            color: var(--mocha, #8b7355);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-size: 0.85rem;
        }

        .admin-table-premium tr.data-row {
            background: rgba(34, 28, 42, 0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
        }

        .admin-table-premium tr.data-row:hover {
            background: rgba(34, 28, 42, 0.8);
            transform: scale(1.005);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .admin-table-premium td {
            padding: 20px;
            border-top: 1px solid rgba(212, 168, 83, 0.05);
            border-bottom: 1px solid rgba(212, 168, 83, 0.05);
        }

        .admin-table-premium td:first-child {
            border-left: 1px solid rgba(212, 168, 83, 0.05);
            border-radius: 8px 0 0 8px;
        }

        .admin-table-premium td:last-child {
            border-right: 1px solid rgba(212, 168, 83, 0.05);
            border-radius: 0 8px 8px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-family: var(--font-accent, sans-serif);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-live {
            background: rgba(178, 34, 34, 0.15);
            color: var(--retro-red, #b22222);
            border: 1px solid rgba(178, 34, 34, 0.3);
        }

        .status-planned {
            background: rgba(212, 168, 83, 0.1);
            color: var(--gold, #d4a853);
            border: 1px solid rgba(212, 168, 83, 0.2);
        }

        .status-down {
            background: rgba(128, 128, 128, 0.1);
            color: #888;
            border: 1px solid rgba(128, 128, 128, 0.3);
        }

        .tab-pane {
            display: none;
            animation: slideInUp 0.6s ease forwards;
        }

        .tab-pane.active {
            display: block;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="film-grain"></div>
    <div class="page-transition active" id="pageTransition">
        <span class="trans-logo">LUMIÈRE</span>
    </div>

    <nav class="lumiere-nav"
        style="background:var(--noir, #0a0a0a); border-bottom:2px solid var(--retro-red, #b22222); display: flex; justify-content: space-between; padding: 15px 5%;">
        <div class="nav-left" style="display:flex; align-items:center;">
            <a href="index.php" class="lumiere-logo">
                <img src="assets/images/logo.svg" alt="LUMIÈRE" style="height:40px;">
            </a>

            <span style="font-family:var(--font-accent, sans-serif); color:var(--retro-red, #b22222); font-size:1.1rem; letter-spacing:0.3em; margin-left:20px; border-left:1px solid #333; padding-left:20px;">DIRECTORATE</span>
        </div>
        <div class="admin-profile" style="display:flex; align-items:center; gap:20px;">
            <div style="text-align:right;">
                <p style="font-family:var(--font-display, serif); color:var(--cream, #fff); margin:0;">
                    <?php echo htmlspecialchars($admin_name); ?>
                </p>
                
                <p style="font-family:var(--font-accent, sans-serif); color:var(--retro-red, #b22222); font-size:0.8rem; margin:0; text-transform:uppercase;">
                    <?php echo htmlspecialchars($admin_tier); ?>
                </p>
            </div>
            <img src="<?php echo htmlspecialchars($admin_avatar); ?>" alt="Admin"
                style="width:45px; height:45px; border-radius:50%; border:2px solid var(--retro-red, #b22222);">
            <button onclick="API.logout()" style="background: none; border: 1px solid var(--retro-red); color: var(--retro-red); padding: 5px 12px; font-family: var(--font-accent); font-size: 0.75rem; border-radius: 4px; cursor: pointer; transition: all 0.3s; margin-left: 10px; letter-spacing: 0.1em;" onmouseover="this.style.background='var(--retro-red)'; this.style.color='white';" onmouseout="this.style.background='none'; this.style.color='var(--retro-red)';">LOG OUT</button>
        </div>
    </nav>

    <div style="display:flex; min-height:100vh; padding-top:80px;">
        <aside class="admin-sidebar">
            <h2>Theatre Control</h2>
            <button class="admin-nav-btn active" onclick="switchAdmin(event, 'overview')">📊 Global Overview</button>
            <button class="admin-nav-btn" onclick="switchAdmin(event, 'catalog')">🎞️ Film Repertoire</button>
            <button class="admin-nav-btn" onclick="switchAdmin(event, 'promotions')">🏷️ Promotions</button>
            <button class="admin-nav-btn" onclick="switchAdmin(event, 'staff')">👥 Staff Directory</button>

            <div style="margin-top:auto; padding-top:20px; border-top:1px solid rgba(255,255,255,0.05);">
                <a href="index.php" class="admin-nav-btn" style="color:var(--mocha, #8b7355);">
                    <span style="font-size:1.2rem;">←</span> Exit to Front
                </a>
            </div>
        </aside>

        <main style="flex:1; padding:60px 5%; background:var(--bg-deep, #120e15);">
            <div id="overview" class="tab-pane active">
                <div class="section-header" style="text-align:left; margin-bottom:50px;">
                    <h1 style="font-style:italic; font-size:3rem; color: var(--cream, #fff);">Executive Summary</h1>
                    <p style="color: var(--cream-dim, #e0d8c8); margin:0;">Performance metrics for the current theatrical week.</p>
                </div>

                <div class="stats-grid"
                    style="display:grid; grid-template-columns:repeat(3, 1fr); gap:30px; margin-bottom:60px;">
                    <div class="stat-card-premium">
                        <span class="stat-label-admin">Total Gross Revenue</span>
                        <div class="stat-value-admin">€ <?php echo number_format($total_gross_all_time, 0); ?></div>
                    </div>

                    <div class="stat-card-premium">
                        <span class="stat-label-admin">Admissions Scanned</span>
                        <div class="stat-value-admin"><?php echo number_format($total_admissions_all_time); ?></div>
                    </div>
                    
                    <div class="stat-card-premium">
                        <span class="stat-label-admin">Active Catalog Size</span>
                        <div class="stat-value-admin"><?php echo $active_catalog_size; ?></div>
                    </div>
                </div>

                <h2 style="margin-bottom:30px; color: var(--cream, #fff);">House Performance</h2>
                <div style="display: flex; flex-direction: column; gap: 30px;">
                    <div class="vintage-card"
                        style="padding:40px; background: var(--bg-card, #1a1520); border: 1px solid rgba(212, 168, 83, 0.15); border-radius: 8px;">
                        <h3 style="color:var(--cream); margin-bottom:15px; font-family:var(--font-accent); border-bottom:1px dashed rgba(212, 168, 83, 0.15); padding-bottom:8px;">Weekly Revenue</h3>
                        <div class="chart-container" style="background:none; border:none; padding:0;">
                            <div class="bar-chart" style="height:250px;">
                                <?php 
                                $days = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'];
                                foreach ($weekly_revenue as $idx => $rev): 
                                    $height = ($rev / $max_rev) * 100;
                                    $color = ($idx >= 3 && $idx <= 5) ? 'var(--retro-red, #b22222)' : 'var(--gold, #d4a853)';
                                ?>
                                    <div class="bar" style="height: <?php echo $height; ?>%; background: <?php echo $color; ?>;" data-value="€<?php echo number_format($rev, 0); ?>"></div>
                                <?php endforeach; ?>
                            </div>
                            <div style="display:flex; justify-content:space-around; margin-top:10px; font-family:var(--font-accent, sans-serif); color:var(--mocha, #8b7355); font-size: 0.85rem;">
                                <?php foreach ($days as $day): ?>
                                    <span style="width: 45px; text-align: center;"><?php echo $day; ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="vintage-card" style="padding:40px; background: var(--bg-card, #1a1520); border: 1px solid rgba(212, 168, 83, 0.15); border-radius: 8px;">
                        <h3 style="color:var(--cream); margin-bottom:15px; font-family:var(--font-accent); border-bottom:1px dashed rgba(212, 168, 83, 0.15); padding-bottom:8px;">Revenue Breakdown by Production</h3>
                        <div style="max-width: 500px; margin: 0 auto;">
                            <canvas id="movieRevenueChart" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <div style="background:var(--bg-card, #1a1520); border:1px solid rgba(212,168,83,0.1); padding:30px; border-radius:8px; margin-top: 30px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
                        <h3 style="color:var(--cream, #fff); font-size:1.4rem;">Current Programme</h3>
                        <button class="btn-primary" style="padding:6px 18px; font-size:0.85rem; background:none; border:1px solid var(--gold); color:var(--gold); border-radius:4px; cursor:pointer;" onclick="switchAdmin(event, 'catalog'); setActiveNav('catalog')">Manage</button>
                    </div>

                    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap:20px;">
                        <?php if (empty($movies)): ?>
                            <p style="color: var(--mocha); width: 100%; text-align: center; padding: 40px;">No movies in repertoire.</p>
                        <?php else: ?>
                            <?php foreach ($movies as $m): ?>
                                <div style="flex: 0 0 200px; border:1px solid rgba(212,168,83,0.1); border-radius:6px; overflow:hidden; cursor:pointer; transition:border-color 0.3s;" onmouseenter="this.style.borderColor='var(--sunset-coral)'" onmouseleave="this.style.borderColor='rgba(212,168,83,0.1)'" onclick="window.location.href='admin_edit_movie.php?id=<?php echo $m['movie_id']; ?>'">
                                    <img src="<?php echo htmlspecialchars($m['poster_path']); ?>" style="width:100%; aspect-ratio:3/4; object-fit:cover; filter:saturate(0.9);" alt="">

                                    <div style="padding:12px; text-align:center; color:var(--gold); font-family:var(--font-accent); text-transform:uppercase; letter-spacing:0.1em; font-size:0.85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo htmlspecialchars($m['movie_name']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <div id="catalog" class="tab-pane">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:40px;">
                    <h1 style="font-style:italic; color: var(--cream, #fff);">Film Repertoire</h1>

                    <button class="btn-coral"
                        style="padding:10px 25px; background: var(--sunset-coral, #e8735a); color: #fff; border: none; border-radius: 4px; cursor: pointer;"
                        onclick="window.location.href='admin_add_movie.php'">
                        + Propose New Screening
                    </button>
                </div>

                <table class="admin-table-premium">
                    <thead>
                        <tr>
                            <th>Production</th>
                            <th>Release</th>
                            <th>Theatrical Status</th>
                            <th>Premium Admission</th>
                            <th style="text-align:right;">Control</th>
                        </tr>
                    </thead>

                    <tbody id="movieTableBody">
                        <?php if (empty($movies)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--mocha); padding: 40px;">Archive empty. Propose a new screening to begin.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($movies as $m): ?>
                                <tr class="data-row">
                                    <td><strong style="font-family:var(--font-display, serif); color:var(--cream, #fff); font-size:1.2rem;">
                                        <?php echo htmlspecialchars($m['movie_name']); ?></strong></td>
                                    <td style="color: var(--cream-dim, #e0d8c8);"><?php echo $m['release_year']; ?></td>
                                    <td>
                                        <?php
                                        $today = new DateTime('today');
                                        $raw_start = (!empty($m['start_date']) && $m['start_date'] !== '0000-00-00') ? $m['start_date'] : '1970-01-01';
                                        $start = new DateTime($raw_start);
                                        $start->setTime(0, 0);

                                        $end = clone $start;
                                        $end->modify('+14 days');

                                        if ($today < $start) {
                                            echo '<span class="status-badge status-planned">Coming Soon</span>';
                                        } elseif ($today >= $start && $today <= $end) {
                                            echo '<span class="status-badge status-live">Live Engagement</span>';
                                        } else {
                                            echo '<span class="status-badge status-down">Down!</span>';
                                        }
                                        ?>
                                    </td>
                                    <td style="color:var(--gold, #d4a853);">€<?php echo number_format(LUMIERE_BASE_PRICE, 2); ?></td>
                                    <td style="text-align:right;">
                                        <button class="btn-primary" style="padding: 5px 15px; font-size: 0.8rem; background: var(--gold); color: var(--bg-deep); border: none; border-radius: 4px; cursor: pointer; font-weight: 600;" onclick="window.location.href='admin_edit_movie.php?id=<?php echo $m['movie_id']; ?>'">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div id="promotions" class="tab-pane">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:40px;">
                    <h1 style="font-style:italic; color: var(--cream, #fff);">Promotion Ledgers</h1>
                    <button class="btn-coral" style="padding:10px 25px; background: var(--sunset-coral, #e8735a); color: #fff; border: none; border-radius: 4px; cursor: pointer;" onclick="window.location.href='admin_set_promotion.php'">
                        + Mint New Coupon
                    </button>
                </div>

                <table class="admin-table-premium">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Offer Detail</th>
                            <th>Code</th>
                            <th>Discount</th>
                            <th>Min. Spend</th>
                            <th style="text-align:right;">Control</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($promos)): ?>
                            <tr><td colspan="6" style="text-align: center; color: var(--mocha); padding: 40px;">No active promotions.</td></tr>
                        <?php else: ?>
                            <?php foreach ($promos as $p): ?>
                                <tr class="data-row">
                                    <td>
                                        <?php if ($p['is_active']): ?>
                                            <span class="status-badge status-live" style="padding: 2px 8px; font-size: 0.7rem;">Active</span>
                                        <?php else: ?>
                                            <span class="status-badge status-down" style="padding: 2px 8px; font-size: 0.7rem;">Paused</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="color: var(--cream, #fff); font-weight: 600;"><?php echo htmlspecialchars($p['description']); ?></div>
                                    </td>
                                    <td style="font-family: monospace; letter-spacing: 0.1em; color: var(--gold);"><?php echo htmlspecialchars($p['promo_code']); ?></td>
                                    <td style="color: var(--cream-dim);">-€<?php echo number_format($p['discount_value'], 2); ?></td>
                                    <td style="color: var(--mocha);">€<?php echo number_format($p['minimum_spend'], 2); ?></td>
                                    <td style="text-align:right;">
                                        <button class="btn-primary" style="padding: 5px 15px; font-size: 0.8rem; background: var(--gold); color: var(--bg-deep); border: none; border-radius: 4px; cursor: pointer; font-weight: 600;" onclick="window.location.href='admin_set_promotion.php?id=<?php echo $p['promotion_id']; ?>'">
                                            Manage
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div id="staff" class="tab-pane">
                <h1 style="font-style:italic; margin-bottom:40px; color: var(--cream, #fff);">Directorate & Operations </h1>

                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:30px;">
                    <div class="stat-card-premium" style="display:flex; align-items:center; gap:20px;">
                        <img src="https://api.dicebear.com/7.x/notionists/svg?seed=Arthur" style="width:60px; height:60px; border-radius:50%; background:var(--gold, #d4a853);">
                        <div>
                            <h4 style="margin:0; font-family:var(--font-display, serif); color: var(--cream, #fff);"> Arthur Pendelton </h4>

                            <p style="margin:0; color:var(--retro-red, #b22222); text-transform:uppercase; font-size:0.75rem;"> Chief Operator </p>
                        </div>
                    </div>
                    <div class="stat-card-premium" style="display:flex; align-items:center; gap:20px;">
                        <img src="https://api.dicebear.com/7.x/notionists/svg?seed=Sarah" style="width:60px; height:60px; border-radius:50%; background:var(--retro-mint, #88c0d0);">
                        <div>
                            <h4 style="margin:0; font-family:var(--font-display, serif); color: var(--cream, #fff);"> Sarah Jenkins </h4>
                            <p style="margin:0; color:var(--retro-mint, #88c0d0); text-transform:uppercase; font-size:0.75rem;"> Lobby Bar Manager </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="js/main.js"></script>
    <script>
        function switchAdmin(event, tabId) {
            if (event) event.preventDefault();

            document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.admin-nav-btn').forEach(el => el.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            if (event) event.currentTarget.classList.add('active');
        }

        function setActiveNav(tabId) {
            document.querySelectorAll('.admin-nav-btn').forEach(btn => {
                btn.classList.remove('active');

                const onclick = btn.getAttribute('onclick');

                if (onclick && onclick.includes(tabId)) {
                    btn.classList.add('active');
                }
            });
        }

        // Chart.js init
        Chart.defaults.color='#9A8B7A'; 
        Chart.defaults.font.family="'EB Garamond', serif";
        
        const movieLabels = <?php echo json_encode($movie_labels); ?>;
        const movieData = <?php echo json_encode($movie_revenue); ?>;
        
        new Chart(document.getElementById('movieRevenueChart'), {
            type: 'doughnut',
            data: {
                labels: movieLabels,
                datasets: [{
                    data: movieData,
                    backgroundColor: ['#D4A853', '#E8735A', '#8B6FA3', '#88C0D0', '#B22222', '#E0D8C8'],
                    borderColor: '#1A1520',
                    borderWidth: 3,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) label += ': ';
                                if (context.parsed !== null) {
                                    label += new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'EUR' }).format(context.parsed);
                                }
                                return label;
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    </script>
</body>

</html>
