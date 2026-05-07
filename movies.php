<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LUMIÈRE - Now Showing</title>
  <meta name="description" content="Browse our curated selection of cinematic masterpieces at LUMIÈRE.">
  <link rel="stylesheet" href="css/base.css?v=5">
    <link rel="stylesheet" href="css/pages/index.css?v=5">
    <link rel="stylesheet" href="css/pages/footer.css?v=5">
    <link rel="stylesheet" href="css/global.css?v=5">
</head>
<body>
  
  
  
  <div class="film-grain"></div>
  <div class="page-transition active" id="pageTransition"><span class="trans-logo">LUMIÈRE</span></div>

  <nav class="lumiere-nav">
    <a href="index.php" class="lumiere-logo" data-no-transition><img src="assets/images/logo.svg?v=5" alt="LUMIÈRE"></a>
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
        <div style="position: absolute; inset: 0; background: radial-gradient(circle, transparent 20%, var(--bg-deep) 95%), linear-gradient(to bottom, transparent 50%, var(--bg-deep) 100%); z-index: 1;"></div>
        
        <h1 class="blur-in" style="position: relative; z-index: 2; margin: 0; font-size: 4rem; text-shadow: 0 0 30px rgba(0,0,0,0.8), 0 5px 15px rgba(0,0,0,0.5); color: #fff;">The Grand Foyer</h1>
        <p style="font-family:var(--font-accent); font-size:1.5rem; color:var(--gold); font-style:italic; position:relative; z-index:2; margin: 15px 0 0; text-shadow: 0 2px 10px rgba(0,0,0,0.8);" class="text-reveal" data-delay="300">Discover the magic of the silver screen</p>
      </div>

      <div class="filter-ribbon fade-up liquidGlass-wrapper" data-delay="300" style="max-width: 850px; margin: -50px auto 60px; padding: 20px 30px; border-radius: 12px; border: 1px solid rgba(212,168,83,0.2); z-index: 10; position: relative;">
        <div class="liquidGlass-effect"></div><div class="liquidGlass-tint" style="background: rgba(13, 11, 14, 0.85);"></div><div class="liquidGlass-shine"></div>
        <div class="liquidGlass-content" style="display:flex; justify-content:space-between; align-items:center; width:100%;">
          <div class="filter-group" style="display:flex; gap:15px; flex-wrap:wrap;">
            <input type="text" id="titleSearch" placeholder="Search by title..." style="background:transparent; border:none; border-bottom:1px solid var(--gold); color:var(--cream); padding:5px; font-family:var(--font-accent); min-width:200px;">
            <select id="genreFilter" style="border-bottom-color: var(--gold);"><option value="all">All Genres</option><option value="Drama">Drama</option><option value="Sci-Fi">Sci-Fi</option><option value="Horror">Horror</option><option value="Thriller">Thriller</option><option value="Comedy">Comedy</option><option value="Adventure">Adventure</option><option value="Crime">Crime</option></select>
            <select id="dateFilter" style="border-bottom-color: var(--gold);"><option value="any">Any Date</option><option value="tonight">Tonight</option><option value="weekend">This Weekend</option></select>
            <select id="priceFilter" style="border-bottom-color: var(--gold);"><option value="any">Any Price</option><option value="low">Price: Low</option><option value="high">Price: High</option></select>
          </div>
          <button id="searchBtn" class="btn-primary" style="padding:10px 25px; font-size:0.95rem; z-index:4; position:relative;">Search Archive</button>
        </div>
      </div>

      <div class="movies-grid">

        <div class="movie-card skew-up" data-delay="100" data-genre="Drama, Thriller" data-price="low" data-price-value="12" data-date="tonight" onclick="triggerPageTransition('movie_oppenheimer.php')">
          <div class="movie-poster-wrap"><img src="assets/images/poster-oppenheimer.png" alt="Oppenheimer"></div>
          <div class="movie-info">
            <h3 class="movie-title">Oppenheimer</h3>
            <p class="movie-meta">2023 · Drama/Thriller · 3h 0m</p>
            <p style="color:var(--sunset-coral); font-size:0.95rem; margin-top:8px; font-family:var(--font-accent);">★★★★★ · From £12</p>
          </div>
        </div>

        <div class="movie-card skew-up" data-delay="200" data-genre="Comedy, Drama" data-price="low" data-price-value="12" data-date="tonight" onclick="triggerPageTransition('movie_grandbudapest.php')">
          <div class="movie-poster-wrap"><img src="assets/images/poster-grandbudapest.png?v=1" alt="Grand Budapest Hotel"></div>
          <div class="movie-info">
            <h3 class="movie-title">Grand Budapest Hotel</h3>
            <p class="movie-meta">2014 · Comedy/Drama · 1h 39m</p>
            <p style="color:var(--sunset-coral); font-size:0.95rem; margin-top:8px; font-family:var(--font-accent);">★★★★★ · From £12</p>
          </div>
        </div>

        <div class="movie-card skew-up" data-delay="300" data-genre="Horror" data-price="low" data-price-value="12" data-date="weekend" onclick="triggerPageTransition('movie_nosferatu.php')">
          <div class="movie-poster-wrap"><img src="assets/images/poster-nosferatu.png" alt="Nosferatu"></div>
          <div class="movie-info">
            <h3 class="movie-title">Nosferatu</h3>
            <p class="movie-meta">2024 · Horror/Gothic · 2h 12m</p>
            <p style="color:var(--sunset-coral); font-size:0.95rem; margin-top:8px; font-family:var(--font-accent);">★★★★★ · From £12</p>
          </div>
        </div>

        <div class="movie-card skew-up" data-delay="400" data-genre="Sci-Fi, Thriller" data-price="high" data-price-value="15" data-date="weekend" onclick="triggerPageTransition('movie_bladerunner.php')">
          <div class="movie-poster-wrap"><img src="assets/images/poster-bladerunner.png" alt="Blade Runner 2049"></div>
          <div class="movie-info">
            <h3 class="movie-title">Blade Runner 2049</h3>
            <p class="movie-meta">2017 · Sci-Fi/Thriller · 2h 44m</p>
            <p style="color:var(--sunset-coral); font-size:0.95rem; margin-top:8px; font-family:var(--font-accent);">★★★★★ · From £15</p>
          </div>
        </div>

        <div class="movie-card skew-up" data-delay="500" data-genre="Sci-Fi, Drama" data-price="low" data-price-value="12" data-date="tonight" onclick="triggerPageTransition('movie_interstellar.php')">
          <div class="movie-poster-wrap"><img src="assets/images/poster-interstellar.png" alt="Interstellar"></div>
          <div class="movie-info">
            <h3 class="movie-title">Interstellar</h3>
            <p class="movie-meta">2014 · Sci-Fi/Drama · 2h 49m</p>
            <p style="color:var(--sunset-coral); font-size:0.95rem; margin-top:8px; font-family:var(--font-accent);">★★★★★ · From £12</p>
          </div>
        </div>

        <div class="movie-card skew-up" data-delay="600" data-genre="Sci-Fi, Adventure" data-price="high" data-price-value="15" data-date="weekend" onclick="triggerPageTransition('movie_dune.php')">
          <div class="movie-poster-wrap"><img src="assets/images/poster-dune.png" alt="Dune Part Two"></div>
          <div class="movie-info">
            <h3 class="movie-title">Dune: Part Two</h3>
            <p class="movie-meta">2024 · Sci-Fi/Adventure · 2h 46m</p>
            <p style="color:var(--sunset-coral); font-size:0.95rem; margin-top:8px; font-family:var(--font-accent);">★★★★★ · From £15</p>
          </div>
        </div>

        <div class="movie-card skew-up" data-delay="700" data-genre="Crime, Drama" data-price="low" data-price-value="12" data-date="tonight" onclick="triggerPageTransition('movie_godfather.php')">
          <div class="movie-poster-wrap"><img src="assets/images/poster-godfather.png?v=1" alt="The Godfather"></div>
          <div class="movie-info">
            <h3 class="movie-title">The Godfather</h3>
            <p class="movie-meta">1972 · Crime/Drama · 2h 55m</p>
            <p style="color:var(--sunset-coral); font-size:0.95rem; margin-top:8px; font-family:var(--font-accent);">★★★★★ · From £12</p>
          </div>
        </div>

        <div class="movie-card skew-up" data-delay="800" data-genre="Sci-Fi, Adventure" data-price="low" data-price-value="12" data-date="weekend" onclick="triggerPageTransition('movie_odyssey.php')">
          <div class="movie-poster-wrap"><img src="assets/images/poster-odyssey.png?v=1" alt="2001: A Space Odyssey"></div>
          <div class="movie-info">
            <h3 class="movie-title">2001: A Space Odyssey</h3>
            <p class="movie-meta">1968 · Sci-Fi/Adventure · 2h 29m</p>
            <p style="color:var(--sunset-coral); font-size:0.95rem; margin-top:8px; font-family:var(--font-accent);">★★★★★ · From £12</p>
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
      const date = document.getElementById('dateFilter').value;
      const priceSort = document.getElementById('priceFilter').value;
      
      const grid = document.querySelector('.movies-grid');
      const cards = Array.from(document.querySelectorAll('.movie-card'));

      // 1. Filtering Logic
      cards.forEach(card => {
        const cardTitle = card.querySelector('.movie-title').textContent.toLowerCase();
        const cardGenres = card.getAttribute('data-genre').split(',').map(g => g.trim());
        const cardDate = card.getAttribute('data-date');
        
        const titleMatch = !title || cardTitle.includes(title);
        const genreMatch = (genre === 'all' || cardGenres.includes(genre));
        const dateMatch = (date === 'any' || cardDate === date);

        if (titleMatch && genreMatch && dateMatch) {
          card.style.display = 'block';
          // Force visibility after display change
          requestAnimationFrame(() => card.classList.add('visible'));
        } else {
          card.style.display = 'none';
        }
      });

      // 2. Sorting Logic (Price)
      if (priceSort !== 'any') {
        const sortedCards = cards.sort((a, b) => {
          const valA = parseFloat(a.getAttribute('data-price-value') || 0);
          const valB = parseFloat(b.getAttribute('data-price-value') || 0);
          return priceSort === 'low' ? valA - valB : valB - valA;
        });

        // Re-append sorted cards to the grid container
        sortedCards.forEach(card => grid.appendChild(card));
      }
    });
  </script>
</body>
</html>
