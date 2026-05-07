<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LUMIÈRE - Where Every Seat Tells a Story</title>
  <meta name="description"
    content="LUMIÈRE vintage cinema - experience the golden age of Hollywood. Browse curated screenings, reserve your seat, and immerse yourself in cinematic magic.">
  <link rel="stylesheet" href="css/base.css?v=5">
    <link rel="stylesheet" href="css/pages/index.css?v=5">
    <link rel="stylesheet" href="css/pages/footer.css?v=5">
    <link rel="stylesheet" href="css/global.css?v=5">
</head>

<body>
  
  
  
  <div class="film-grain"></div>

  <div class="page-transition active" id="pageTransition">
    <span class="trans-logo">LUMIÈRE</span>
  </div>

  <nav class="lumiere-nav">
    <a href="index.php" class="lumiere-logo" data-no-transition><img src="assets/images/logo.svg?v=5"
        alt="LUMIÈRE"></a>
    <div class="nav-links">
      <a href="index.php" class="nav-link" data-no-transition style="color:var(--sunset-coral);">Home</a>
      <a href="movies.php" class="nav-link">Now Showing</a>
      <a href="history.php" class="nav-link">My Tickets</a>
      <a href="dashboard_user.php" class="nav-link">Account</a>
      <a href="about.php" class="nav-link">The Cinema</a>
    </div>
  </nav>

  <section class="home-hero">
    <div class="hero-bg" style="background-image:url('assets/images/hero-bg.png');"></div>
    <div class="hero-glow"></div>

    <div class="hero-content reveal">
      <img src="assets/images/logo.svg?v=5" alt="LUMIÈRE" class="hero-logo blur-in">
      <h1 class="blur-in" data-delay="600"
        style="color:var(--cream); font-size:clamp(2.5rem,5vw,4.5rem); font-style:italic; margin-bottom:15px; text-shadow:0 0 30px rgba(232,115,90,0.4);">
        Where Every Seat Tells a Story</h1>
      <p class="hero-tagline text-reveal" data-delay="1200">Step into the golden age of cinema. Live. Breathe.
        Experience.</p>
      <div class="hero-cta skew-up" data-delay="1500">
        <a href="movies.php" class="btn-coral"
          style="background:linear-gradient(135deg, var(--retro-red), var(--sunset-coral));">Explore Screenings</a>
        <a href="about.php" class="btn-primary">Our Heritage</a>
      </div>
    </div>

    <div class="scroll-hint">
      <span>Discover</span>
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 5v14M19 12l-7 7-7-7" />
      </svg>
    </div>
  </section>

  <div
    style="background: linear-gradient(90deg, var(--retro-red), var(--sunset-coral), var(--retro-mustard), var(--retro-olive)); padding:12px 0; overflow:hidden; box-shadow: 0 4px 20px rgba(232,115,90,0.3);">
    <div class="marquee">
      <span>Now Showing: Oppenheimer</span>
      <span>This Weekend: The Grand Budapest Hotel</span>
      <span>Coming Soon: Nosferatu</span>
      <span>Restored Classic: Blade Runner 2049</span>
      <span>Now Showing: Oppenheimer</span>
      <span>This Weekend: The Grand Budapest Hotel</span>
      <span>Coming Soon: Nosferatu</span>
      <span>Restored Classic: Blade Runner 2049</span>
    </div>
  </div>

  <section class="featured-section">
    <div class="section-header fade-up">
      <h2>On the Silver Screen</h2>
      <div class="divider" style="max-width:300px; margin:20px auto 15px;"></div>
      <p>Our hand-picked programme of cinematic masterpieces, curated for the discerning patron.</p>
    </div>

    <div class="movies-grid">
      <div class="movie-card skew-up" data-delay="0" onclick="triggerPageTransition('movie_oppenheimer.php')">
        <div class="movie-poster-wrap"><img src="assets/images/poster-oppenheimer.png" alt="Oppenheimer"></div>
        <div class="movie-info">
          <h3 class="movie-title">Oppenheimer</h3>
          <p class="movie-meta">2023 · Drama/Thriller · 3h 0m</p>
          <p style="color:var(--sunset-coral); font-size:0.95rem; margin-top:8px; font-family:var(--font-accent);">★★★★★
            · From £12</p>
        </div>
      </div>

      <div class="movie-card skew-up" data-delay="100" onclick="triggerPageTransition('movie_grandbudapest.php')">
        <div class="movie-poster-wrap"><img src="assets/images/poster-grandbudapest.png?v=1" alt="Grand Budapest Hotel">
        </div>
        <div class="movie-info">
          <h3 class="movie-title">Grand Budapest Hotel</h3>
          <p class="movie-meta">2014 · Comedy/Drama · 1h 39m</p>
          <p style="color:var(--sunset-coral); font-size:0.95rem; margin-top:8px; font-family:var(--font-accent);">★★★★★ ·
            From £12</p>
        </div>
      </div>

      <div class="movie-card skew-up" data-delay="200" onclick="triggerPageTransition('movie_nosferatu.php')">
        <div class="movie-poster-wrap"><img src="assets/images/poster-nosferatu.png" alt="Nosferatu"></div>
        <div class="movie-info">
          <h3 class="movie-title">Nosferatu</h3>
          <p class="movie-meta">2024 · Horror/Gothic · 2h 12m</p>
          <p style="color:var(--sunset-coral); font-size:0.95rem; margin-top:8px; font-family:var(--font-accent);">★★★★★
            · From £12</p>
        </div>
      </div>

      <div class="movie-card skew-up" data-delay="300" onclick="triggerPageTransition('movie_bladerunner.php')">
        <div class="movie-poster-wrap"><img src="assets/images/poster-bladerunner.png" alt="Blade Runner 2049"></div>
        <div class="movie-info">
          <h3 class="movie-title">Blade Runner 2049</h3>
          <p class="movie-meta">2017 · Sci-Fi/Thriller · 2h 44m</p>
          <p style="color:var(--sunset-coral); font-size:0.95rem; margin-top:8px; font-family:var(--font-accent);">★★★★★ ·
            From £15</p>
        </div>
      </div>

      <div class="movie-card skew-up" data-delay="400" onclick="triggerPageTransition('movie_interstellar.php')">
        <div class="movie-poster-wrap"><img src="assets/images/poster-interstellar.png" alt="Interstellar"></div>
        <div class="movie-info">
          <h3 class="movie-title">Interstellar</h3>
          <p class="movie-meta">2014 · Sci-Fi/Drama · 2h 49m</p>
          <p style="color:var(--sunset-coral); font-size:0.95rem; margin-top:8px; font-family:var(--font-accent);">★★★★★
            · From £12</p>
        </div>
      </div>

      <div class="movie-card skew-up" data-delay="500" onclick="triggerPageTransition('movie_dune.php')">
        <div class="movie-poster-wrap"><img src="assets/images/poster-dune.png" alt="Dune Part Two"></div>
        <div class="movie-info">
          <h3 class="movie-title">Dune: Part Two</h3>
          <p class="movie-meta">2024 · Sci-Fi/Adventure · 2h 46m</p>
          <p style="color:var(--sunset-coral); font-size:0.95rem; margin-top:8px; font-family:var(--font-accent);">
            ★★★★★ · From £15</p>
        </div>
      </div>

      <div class="movie-card skew-up" data-delay="600" onclick="triggerPageTransition('movie_godfather.php')">
        <div class="movie-poster-wrap"><img src="assets/images/poster-godfather.png?v=1" alt="The Godfather"></div>
        <div class="movie-info">
          <h3 class="movie-title">The Godfather</h3>
          <p class="movie-meta">1972 · Crime/Drama · 2h 55m</p>
          <p style="color:var(--sunset-coral); font-size:0.95rem; margin-top:8px; font-family:var(--font-accent);">
            ★★★★★ · From £12</p>
        </div>
      </div>

      <div class="movie-card skew-up" data-delay="700" onclick="triggerPageTransition('movie_odyssey.php')">
        <div class="movie-poster-wrap"><img src="assets/images/poster-odyssey.png?v=1" alt="2001: A Space Odyssey">
        </div>
        <div class="movie-info">
          <h3 class="movie-title">2001: A Space Odyssey</h3>
          <p class="movie-meta">1968 · Sci-Fi/Adventure · 2h 29m</p>
          <p style="color:var(--sunset-coral); font-size:0.95rem; margin-top:8px; font-family:var(--font-accent);">★★★★★ ·
            From £12</p>
        </div>
      </div>
    </div>
  </section>

  <div class="divider"></div>

  <section class="experience-section">
    <div class="section-header fade-up">
      <h2>The LUMIÈRE Experience</h2>
      <div class="divider" style="max-width:300px; margin:20px auto 15px;"></div>
      <p>More than a cinema. An immersion into the world of moving pictures.</p>
    </div>

    <div class="experience-grid">
      <div class="exp-card fade-up" data-delay="0">
        <span class="exp-icon">🎞️</span>
        <h3>35mm Projection</h3>
        <p>Every screening is projected from restored 35mm prints or state-of-the-art 4K laser, preserving the
          director's original vision.</p>
      </div>
      <div class="exp-card fade-up" data-delay="150">
        <span class="exp-icon">🍸</span>
        <h3>The Lobby Bar</h3>
        <p>Sip on hand-crafted cocktails inspired by classic films. From "The Maltese Fizz" to "Sunset Boulevard Sour" -
          cinema in a glass.</p>
      </div>
      <div class="exp-card fade-up" data-delay="300">
        <span class="exp-icon">💺</span>
        <h3>Velvet Seating</h3>
        <p>Restored 1940s sprung velvet seats, each one hand-reupholstered. Choose from Stalls, Circle, or our exclusive
          Royal Box.</p>
      </div>
    </div>
  </section>

  <div class="divider"></div>

  <section class="story-grid fade-up" style="max-width: 1200px; margin: 150px auto; padding: 0 5%;">
    <div class="story-content">
      <h2 style="font-size: 3rem; margin-bottom: 25px;">A Century of Light</h2>
      <p><span class="dropcap">B</span>orn in the dawn of the 20th century, LUMIÈRE has stood as a bastion of the moving
        image for over a hundred years. Inspired by the pioneers of cinematography, our theatre preserves the sacred
        ritual of movie-going-the collective gasp, the shared silence, and the magic of a silver screen coming to life.
      </p>
      <p>Recently restored to its 1920s art-deco grandeur, we offer an experience that digital multiplexes cannot
        replicate: the warmth of 35mm film, the embrace of sprung velvet seats, and the refined elegance of a bygone
        era.</p>
      <a href="about.php" class="btn-primary" style="margin-top:20px;">Read Our Full History</a>
    </div>
    <div class="story-visual">
      <img src="assets/images/hero-bg.png"
        style="width: 100%; height: 450px; object-fit: cover; border-radius: 2px; box-shadow: 0 30px 80px rgba(0,0,0,0.6);">
    </div>
  </section>

  <div class="divider"></div>

  <section class="featured-section">
    <div class="section-header fade-up">
      <h2>From Our Patrons</h2>
      <div class="divider" style="max-width:300px; margin:20px auto 15px;"></div>
    </div>
    <div class="reviews-grid" style="max-width:1100px; margin:0 auto;">
      <div class="review-card fade-up" data-delay="0">
        <div class="ink-stars">★★★★★</div>
        <p class="review-text">"Walking into LUMIÈRE is like stepping into a time machine. The velvet seats, the golden
          light, the crackle of the projector - pure magic."</p>
        <span class="review-author">- Eleanor V., Member since 2019</span>
      </div>
      <div class="review-card fade-up" data-delay="150">
        <div class="ink-stars">★★★★★</div>
        <p class="review-text">"I drove three hours to see Oppenheimer here. On 35mm film, in those seats, with that
          sound - it was the experience of a lifetime."</p>
        <span class="review-author">- Marcus T., First-time visitor</span>
      </div>
      <div class="review-card fade-up" data-delay="300">
        <div class="ink-stars">★★★★★</div>
        <p class="review-text">"The Lobby Bar alone is worth the trip. But knowing your ticket supports real film
          preservation? That's something special."</p>
        <span class="review-author">- Sofia R., Annual pass holder</span>
      </div>
    </div>
  </section>

  <div class="divider"></div>

  <section class="stats-container" id="statsBox">
    <div class="stat-box fade-up" data-delay="0">
      <div class="stat-number" id="scFilms">0</div>
      <div class="stat-label">Films Screened</div>
    </div>
    <div class="stat-box fade-up" data-delay="150">
      <div class="stat-number" id="scSeats">0</div>
      <div class="stat-label">Seats Booked</div>
    </div>
    <div class="stat-box fade-up" data-delay="300">
      <div class="stat-number" id="scYears">0</div>
      <div class="stat-label">Years of History</div>
    </div>
    <div class="stat-box fade-up" data-delay="450">
      <div class="stat-number" id="scMembers">0</div>
      <div class="stat-label">Members</div>
    </div>
  </section>

  <section class="cta-section">
    <h2 class="fade-up" style="font-style:italic;">The Curtain Rises at Sunset</h2>
    <p class="fade-up" data-delay="100">Reserve your seat for tonight's screening and become part of our story.</p>
    <div class="fade-up" data-delay="200"
      style="display:flex; gap:20px; justify-content:center; flex-wrap:wrap; position:relative; z-index:2;">
      <a href="movies.php" class="btn-coral">Book Tickets</a>
      <a href="index_login.php" class="btn-primary">Sign In</a>
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

  <script src="js/main.js?v=5"></script>
  <script>

    function animateCounter(el, target, dur) {
      let start = 0;
      const step = target / ((dur / 1000) * 60);
      function tick() {
        start += step;
        if (start >= target) { el.textContent = target.toLocaleString(); return; }
        el.textContent = Math.floor(start).toLocaleString();
        requestAnimationFrame(tick);
      }
      tick();
    }
    const obs = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          animateCounter(document.getElementById('scFilms'), 14502, 2500);
          animateCounter(document.getElementById('scSeats'), 342980, 2500);
          animateCounter(document.getElementById('scYears'), 104, 2000);
          animateCounter(document.getElementById('scMembers'), 2847, 2200);
          obs.disconnect();
        }
      });
    });
    obs.observe(document.getElementById('statsBox'));
  </script>
</body>

</html>