<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index_login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUMIÈRE - Now Showing</title>
    <meta name="description" content="Browse our curated selection of cinematic masterpieces at LUMIÈRE.">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/pages/index.css">
    <link rel="stylesheet" href="css/pages/footer.css">
    <link rel="stylesheet" href="css/global.css">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
</head>

<body>
    <div class="film-grain"></div>
    <div class="page-transition active" id="pageTransition">
        <span class="trans-logo">LUMIÈRE</span>
    </div>

    <nav class="lumiere-nav">
        <a href="index.php" class="lumiere-logo" data-no-transition>
            <img src="assets/images/logo.svg" alt="LUMIÈRE">
        </a>

        <div class="nav-links">
            <a href="index.php" class="nav-link">Home</a>
            <a href="movies.php" class="nav-link" style="color:var(--sunset-coral);">Now Showing</a>
            <a href="history.php" class="nav-link">My Tickets</a>
            <a href="dashboard_user.php" class="nav-link">Account</a>
            <a href="about.php" class="nav-link">The Cinema</a>
        </div>
    </nav>

    <div class="page-wrapper" style="padding-top: 100px;">
        <section class="movies-section" style="padding-top: 40px;">
            <div class="dashboard-header" style="max-width: 900px; margin: 0 auto 30px; border-radius: 15px; min-height: 400px; display: flex; flex-direction: column; justify-content: center; align-items: center; box-shadow: 0 30px 60px rgba(0,0,0,0.5); position: relative; overflow: hidden; background: var(--bg-deep);">
                <img src="assets/images/hero-bg.png" alt="" class="bg-img" style="width: 100%; height: 100%; object-fit: cover; object-position: center 30%; position: absolute; top: 0; left: 0; filter: brightness(0.4) saturate(1.2);">
                <div style="position: absolute; inset: 0; background: radial-gradient(  ellipse at center,  transparent 20%,  rgba(0, 0, 0, 0.1) 40%,  var(--bg-deep) 70%); z-index: 1;"></div>
                
                <h1 class="blur-in" style="position: relative; z-index: 2; margin: 0; font-size: 4rem; text-shadow: 0 0 30px rgba(0,0,0,0.8), 0 5px 15px rgba(0,0,0,0.5); color: #fff;">The Grand Foyer</h1>
                <p style="font-family:var(--font-accent); font-size:1.5rem; color:var(--gold); font-style:italic; position:relative; z-index:2; margin: 15px 0 0; text-shadow: 0 2px 10px rgba(0,0,0,0.8);" class="text-reveal" data-delay="300">Discover the magic of the silver screen</p>
            </div>

            <div class="filter-ribbon fade-up liquidGlass-wrapper" data-delay="300" style="max-width: 850px; margin: -50px auto 60px; padding: 20px 30px; border-radius: 12px; border: 1px solid rgba(212,168,83,0.2); z-index: 10; position: relative;">
                <div class="liquidGlass-effect"></div>
                <div class="liquidGlass-tint" style="background: rgba(13, 11, 14, 0.85);"></div>
                <div class="liquidGlass-shine"></div>
                <div class="liquidGlass-content" style="display:flex;justify-content:space-between; align-items:center; width:100%;">
                    <div class="filter-group" style="display:flex; gap:25px; flex-wrap:wrap; flex: 1; margin-right: 40px;">
                        <input type="text" id="titleSearch" placeholder="Search by title..." style="background:transparent; border:none; border-bottom:1px solid var(--gold); color:var(--cream); padding:5px; font-family:var(--font-accent); font-size: 1.15rem; flex: 1.5; min-width:200px;">

                        <select id="genreFilter" style="border-bottom-color: var(--gold); flex: 1;">
                            <option value="all">All Genres</option>
                            <option value="Action">Action</option>
                            <option value="Adventure">Adventure</option>
                            <option value="Comedy">Comedy</option>
                            <option value="Crime">Crime</option>
                            <option value="Drama">Drama</option>
                            <option value="Fantasy">Fantasy</option>
                            <option value="Historical">Historical</option>
                            <option value="Horror">Horror</option>
                            <option value="Musical">Musical</option>
                            <option value="Romance">Romance</option>
                            <option value="Sci-Fi">Sci-Fi</option>
                            <option value="Thriller">Thriller</option>
                        </select>

                        <select id="priceFilter" style="border-bottom-color: var(--gold); flex: 1;">
                            <option value="any">Any Price</option>
                            <option value="low">Price: Low</option>
                            <option value="high">Price: High</option>
                        </select>
                    </div>

                    <button id="searchBtn" class="btn-primary" style="padding:10px 25px; font-size:0.95rem; z-index:4; position:relative;">Search Archive</button>
                </div>
            </div>

            <div class="movies-grid">
                <?php 
                $result = mysqli_query($conn, "SELECT * FROM movies 
                                               WHERE start_date IS NOT NULL 
                                                  AND start_date != '0000-00-00'
                                                  AND CURDATE() >= start_date 
                                                  AND CURDATE() <= DATE_ADD(start_date, INTERVAL 14 DAY)");
                while($row = mysqli_fetch_assoc($result)): 
                ?>

                <div class="movie-card skew-up" data-genre="<?= $row['genre'] ?>" onclick="window.location.href='movie.php?movie_id=<?= $row['movie_id'] ?>'">
                    <div class="movie-poster-wrap">
                        <img src="<?= $row['poster_path'] ?>" alt="<?= htmlspecialchars($row['movie_name']) ?>">
                    </div>
                    <div class="movie-info">
                        <h3 class="movie-title"><?= htmlspecialchars($row['movie_name']) ?></h3>
                        <p class="movie-meta"><?= $row['release_year'] ?> · <?= $row['genre'] ?> · <?= $row['duration'] ?>m</p>
                        <p style="color:var(--sunset-coral);">From €<?= number_format(LUMIERE_BASE_PRICE, 2) ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </section>
    </div>

    <footer>
        <img src="assets/images/logo.svg" alt="LUMIÈRE" class="logo-img">
        <p>Where Every Seat Tells a Story.</p>
        <div class="footer-links">
            <a href="movies.php">Now Showing</a>
            <a href="about.php">The Cinema</a>
            <a href="dashboard_user.php">Account</a>
            <a href="dashboard_admin.php">Staff Area</a>
        </div>
        <p style="margin-top:30px; font-size:0.9rem; opacity:0.5;">© 2026 LUMIÈRE Cinemas. All rights reserved.</p>
    </footer>

    <script src="js/main.js"></script>
    <svg width="0" height="0" style="position: absolute;">
        <filter id="glass-distortion">
            <feTurbulence type="fractalNoise" baseFrequency="0.04" numOctaves="1" result="noise" />
            <feDisplacementMap in="SourceGraphic" in2="noise" scale="4" xChannelSelector="R" yChannelSelector="G" />
        </filter>
    </svg>

    <script>
        document.getElementById('searchBtn').addEventListener('click', function() {
            const title = document.getElementById('titleSearch').value.toLowerCase().trim();
            const genre = document.getElementById('genreFilter').value;
            const priceSort = document.getElementById('priceFilter').value;
            
            const grid = document.querySelector('.movies-grid');
            const cards = Array.from(document.querySelectorAll('.movie-card'));

            // Filtering Logic
            cards.forEach(card => {
                const cardTitle = card.querySelector('.movie-title').textContent.toLowerCase();
                const cardGenres = card.getAttribute('data-genre').split(',').map(g => g.trim());
                const titleMatch = !title || cardTitle.includes(title);
                const genreMatch = (genre === 'all' || cardGenres.includes(genre));

                if (titleMatch && genreMatch) {
                    card.style.display = 'block';
                    requestAnimationFrame(() => card.classList.add('visible'));
                } else {
                    card.style.display = 'none';
                }
            });

            // Sorting
            if (priceSort !== 'any') {
                const sortedCards = cards.sort((a, b) => {
                    const valA = parseFloat(a.getAttribute('data-price-value') || 0);
                    const valB = parseFloat(b.getAttribute('data-price-value') || 0);
                    return priceSort === 'low' ? valA - valB : valB - valA;
                });
                sortedCards.forEach(card => grid.appendChild(card));
            }
        });
    </script>
</body>
</html>
