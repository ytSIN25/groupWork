<?php
require_once 'config.php';
$mid = $_GET['movie_id'] ?? 1;
$stmt = $conn->prepare("SELECT * FROM movies WHERE movie_id = ?");
$stmt->bind_param("i", $mid);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$movie) {
        header('Location: movies.php');
        exit();
}

// Fetch user preference
$user_pref_tier = 'Stalls';
if (isset($_SESSION['user_id'])) {
        $stmt_p = $conn->prepare("SELECT preferred_seating FROM user_preferences WHERE user_id = ?");
        $stmt_p->bind_param("i", $_SESSION['user_id']);
        $stmt_p->execute();
        $pref_res = $stmt_p->get_result();
        if ($pref = $pref_res->fetch_assoc()) {
                $ps = $pref['preferred_seating'];
                if (strpos($ps, 'Circle') !== false) $user_pref_tier = 'Circle';
                elseif (strpos($ps, 'Royal') !== false) $user_pref_tier = 'Royal Box';
        }
        $stmt_p->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUMIÈRE - <?= htmlspecialchars($movie['movie_name']) ?></title>
    <meta name="description" content="<?= htmlspecialchars(substr($movie['description'], 0, 150)) ?>... Now showing at LUMIÈRE.">
    <link rel="stylesheet" href="css/base.css?v=5">
    <link rel="stylesheet" href="css/pages/movie-details.css?v=5">
    <link rel="stylesheet" href="css/pages/booking.css?v=5">
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
            <a href="history.php" class="nav-link">My Tickets</a>
            <a href="about.php" class="nav-link">The Cinema</a>
        </div>
    </nav>

    <div class="page-wrapper">
        <header class="detail-hero">
            <img src="<?= htmlspecialchars($movie['poster_path']) ?>" alt="" class="bg-img kb-zoom">
            <div class="detail-content fade-up">
                <div class="detail-tagline" data-delay="100">LUMIÈRE Selection</div>
                <h1 class="detail-title"><?= htmlspecialchars($movie['movie_name']) ?></h1>
                <div class="detail-meta">
                    <span><?= $movie['release_year'] ?></span>
                    <span><?= $movie['duration'] ?> Minutes</span>
                    <span><?= htmlspecialchars($movie['director']) ?></span>
                    <span><?= $movie['genre'] ?></span>
                </div>
                <div class="divider" style="margin:20px 0;"></div>
            </div>
        </header>

        <section class="editorial-section">
            <div class="editorial-flex">
                <div class="editorial-left fade-up">
                    <h2 style="margin-bottom:25px;">The Tale</h2>
                    <p class="detail-synopsis">
                        <?= nl2br(htmlspecialchars($movie['description'])) ?>
                    </p>

                    <div class="reviews-section">
                        <h2 style="margin-bottom:25px;">Critic Reviews</h2>
                        <div class="reviews-grid">
                            <div class="review-card">
                                <div class="ink-stars">★★★★★</div>
                                <p class="review-text">"A stunning achievement in cinema. This is why we go to the movies."</p>
                                <span class="review-author">- The Lumière Daily</span>
                            </div>
                            
                            <div class="review-card">
                                <div class="ink-stars">★★★★★</div>
                                <p class="review-text">"Immersive, beautiful, and profoundly moving. A must-watch for any cinephile."</p>
                                <span class="review-author">- Parisian Film Review</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="editorial-right fade-right">
                    <h2 style="font-size:1.5rem; margin-bottom:25px;">Starring</h2>
                    <ul class="cast-list">
                        <?php 
                        $stars = explode(',', $movie['starring']);
                        foreach($stars as $star):
                        ?>
                        <li><span class="cast-name"><?= htmlspecialchars(trim($star)) ?></span></li>
                        <?php endforeach; ?>
                    </ul>

                    <div style="margin-top:40px;">
                        <h3 style="font-size:1.5rem; margin-bottom:15px;">Purchase Admission</h3>
                        <div class="ticket-tiers" id="tierSelector">
                            <button type="button" class="ticket-tier <?= ($user_pref_tier === 'Stalls') ? 'selected' : '' ?>" data-tier="Stalls" onclick="selectTier(this)">
                                <div>
                                    <div class="tier-name">Stalls</div>
                                    <div style="font-size:0.9rem; color:var(--mocha); font-style:italic;">Main level</div>
                                </div>
                                <div class="tier-price">€<?= number_format(LUMIERE_BASE_PRICE, 2) ?></div>
                            </button>

                            <button type="button" class="ticket-tier <?= ($user_pref_tier === 'Circle') ? 'selected' : '' ?>" data-tier="Circle" onclick="selectTier(this)">
                                <div>
                                    <div class="tier-name">Circle</div>
                                    <div style="font-size:0.9rem; color:var(--mocha); font-style:italic;">Balcony view</div>
                                </div>
                                <div class="tier-price">€<?= number_format(LUMIERE_BASE_PRICE * 1.5, 2) ?></div>
                            </button>

                            <button type="button" class="ticket-tier <?= ($user_pref_tier === 'Royal Box') ? 'selected' : '' ?>" data-tier="Royal Box" onclick="selectTier(this)">
                                <div>
                                    <div class="tier-name">Royal Box</div>
                                    <div style="font-size:0.9rem; color:var(--mocha); font-style:italic;">Private booth</div>
                                </div>
                                <div class="tier-price">€<?= number_format(LUMIERE_BASE_PRICE * 3, 2) ?></div>
                            </button>
                        </div>
                        <button class="btn-coral" style="width:100%; margin-top:25px; background:linear-gradient(135deg, var(--retro-red), var(--retro-red-glow));" onclick="proceedToBooking()">Select Seats</button>
                        
                        <script>
                            function selectTier(el) {
                                document.querySelectorAll('.ticket-tier').forEach(t => t.classList.remove('selected'));
                                el.classList.add('selected');
                            }
                            
                            function proceedToBooking() {
                                const selected = document.querySelector('.ticket-tier.selected');
                                const tier = selected ? selected.getAttribute('data-tier') : 'Stalls';
                                triggerPageTransition(`booking.php?movie_id=<?= $movie['movie_id'] ?>&tier=${encodeURIComponent(tier)}`);
                            }
                        </script>
                    </div>
                </div>
            </div>
        </section>

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
    </div>
    <script src="js/main.js?v=5"></script>
</body>
</html>