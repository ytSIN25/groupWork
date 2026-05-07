<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Learn about the heritage and restoration of LUMIÈRE Cinemas.">
  <title>LUMIÈRE - Our Philosophy & Heritage</title>
  <link rel="stylesheet" href="css/base.css?v=5">
    <link rel="stylesheet" href="css/pages/index.css?v=5">
    <link rel="stylesheet" href="css/pages/footer.css?v=5">
    <link rel="stylesheet" href="css/global.css?v=5">
  <style>
    /* Specific About Page Styles */
    .about-hero {
      position: relative;
      height: 80vh;
      min-height: 600px;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      margin-top: 80px;
    }
    
    .about-hero::after {
      content: '';
      position: absolute;
      top: 0; left: 0; width: 100%; height: 100%;
      background: linear-gradient(180deg, transparent 0%, var(--bg-deep) 100%);
      z-index: 1;
    }

    .about-hero img {
      position: absolute;
      top: 0; left: 0; width: 100%; height: 100%;
      object-fit: cover;
      filter: brightness(0.4) saturate(1.2) contrast(1.1);
      z-index: 0;
      transform: scale(1.05);
      animation: ken-burns 30s ease-out infinite alternate;
    }

    .about-title-wrapper {
      position: relative;
      z-index: 2;
      text-align: center;
      padding: 0 5%;
    }

    .about-tag {
      font-family: var(--font-accent);
      color: var(--gold);
      font-size: 1.3rem;
      letter-spacing: 0.3em;
      text-transform: uppercase;
      margin-bottom: 2rem;
      display: inline-block;
      position: relative;
    }

    .about-tag::before, .about-tag::after {
      content: '';
      position: absolute;
      top: 50%;
      width: 40px;
      height: 1px;
      background: var(--gold);
      opacity: 0.5;
    }
    .about-tag::before { right: 100%; margin-right: 15px; }
    .about-tag::after { left: 100%; margin-left: 15px; }

    .about-title {
      font-size: clamp(3.5rem, 8vw, 7rem);
      line-height: 1;
      margin-bottom: 1.5rem;
      text-shadow: 0 10px 40px rgba(0,0,0,0.8);
    }

    .about-subtitle {
      font-family: var(--font-calligraphy);
      font-size: clamp(2rem, 3vw, 3rem);
      color: var(--sunset-coral);
      opacity: 0.9;
    }

    .about-philosophy {
      max-width: 900px;
      margin: 120px auto;
      text-align: center;
      padding: 0 5%;
    }

    .about-philosophy p {
      font-size: 1.3rem;
      line-height: 2.2;
      color: var(--cream);
      margin-bottom: 2rem;
    }

    .about-philosophy .dropcap {
      float: left;
      font-family: var(--font-display);
      font-size: 5rem;
      line-height: 0.8;
      padding-top: 4px;
      padding-right: 15px;
      color: var(--gold);
    }

    .legacy-section {
      padding: 100px 5%;
      position: relative;
      background: var(--bg-dark);
      border-top: 1px solid rgba(212, 168, 83, 0.1);
      border-bottom: 1px solid rgba(212, 168, 83, 0.1);
    }

    .legacy-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px;
      max-width: 1300px;
      margin: 0 auto;
      align-items: center;
    }

    .legacy-image-wrapper {
      position: relative;
      padding: 30px;
    }

    .legacy-image-wrapper::before {
      content: '';
      position: absolute;
      top: 0; left: 0; width: 100%; height: 100%;
      border: 1px solid var(--gold);
      opacity: 0.3;
      transform: translate(-15px, -15px);
      transition: transform 0.5s var(--ease-smooth);
    }

    .legacy-image-wrapper:hover::before {
      transform: translate(-5px, -5px);
    }

    .legacy-image {
      width: 100%;
      border-radius: 4px;
      filter: sepia(0.3) grayscale(0.2) contrast(1.1);
      box-shadow: 0 25px 60px rgba(0,0,0,0.8);
      position: relative;
      z-index: 2;
    }

    .legacy-content h2 {
      font-size: 3.5rem;
      margin-bottom: 1.5rem;
      color: var(--cream);
    }

    .legacy-content p {
      font-size: 1.15rem;
      margin-bottom: 1.5rem;
      opacity: 0.85;
    }
    
    .stats-showcase {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 40px;
      max-width: 1100px;
      margin: 150px auto;
      padding: 0 5%;
    }

    .stat-card {
      text-align: center;
      padding: 50px 30px;
      background: linear-gradient(145deg, rgba(34, 28, 42, 0.4), rgba(26, 21, 32, 0.6));
      border: 1px solid rgba(212, 168, 83, 0.1);
      border-radius: 4px;
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; width: 100%; height: 2px;
      background: linear-gradient(90deg, transparent, var(--gold), transparent);
      opacity: 0.5;
    }

    .stat-num {
      font-family: var(--font-display);
      font-size: 4.5rem;
      color: var(--gold);
      margin-bottom: 10px;
      text-shadow: 0 0 20px rgba(212, 168, 83, 0.3);
    }

    .stat-title {
      font-family: var(--font-accent);
      font-size: 1.2rem;
      text-transform: uppercase;
      letter-spacing: 0.2em;
      color: var(--cream-dim);
    }

    @media (max-width: 900px) {
      .legacy-grid { grid-template-columns: 1fr; gap: 40px; }
      .legacy-grid:nth-child(even) .legacy-image-wrapper { grid-row: 2; }
      .stats-showcase { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  
  <!-- System Effects -->
  
  
  <div class="film-grain"></div>
  
  <div class="page-transition active" id="pageTransition">
    <span class="trans-logo">LUMIÈRE</span>
  </div>

  <!-- Navigation -->
  <nav class="lumiere-nav">
    <a href="index.php" class="lumiere-logo" data-no-transition>
      <img src="assets/images/logo.svg?v=5" alt="LUMIÈRE">
    </a>
    <div class="nav-links">
      <a href="index.php" class="nav-link">Home</a>
      <a href="movies.php" class="nav-link">Now Showing</a>
      <a href="history.php" class="nav-link">My Tickets</a>
      <a href="dashboard_user.php" class="nav-link">Account</a>
      <a href="about.php" class="nav-link" style="color:var(--sunset-coral); border-bottom: 1.5px solid var(--sunset-coral);">The Cinema</a>
    </div>
  </nav>

  <div class="page-wrapper">
    <!-- Hero Section -->
    <header class="about-hero">
      <img src="assets/images/hero-bg.png" alt="Cinema Hall">
      <div class="about-title-wrapper reveal">
        <span class="about-tag">Established 1922</span>
        <h1 class="about-title">A Sanctuary for Cinema</h1>
        <div class="about-subtitle">Where every frame tells a story</div>
      </div>
    </header>

    <!-- Core Philosophy -->
    <section class="about-philosophy reveal">
      <p><span class="dropcap">T</span>o sit in the dark and witness light paint dreams upon a wall - this is the magic we curate. At LUMIÈRE, we believe that cinema is not merely a sequence of frames, but a visceral, communal experience that has the power to transcend time. Our theatre was founded by visionaries whose very name became synonymous with the birth of moving pictures.</p>
      <p>We are not a multiplex. We are a temple of storytelling, dedicated to those who appreciate the flicker of a 35mm projector, the rich velvety acoustics of a classic acoustic hall, and the deep, shared silence of an audience held captive by a masterpiece.</p>
    </section>

    <!-- Legacy / History Section -->
    <section class="legacy-section">
      <div class="legacy-grid reveal">
        <div class="legacy-content">
          <h2>The Golden Age Restored</h2>
          <p>In 2024, LUMIÈRE underwent a meticulous three-year restoration. We worked with master craftsmen to reupholster every velvet seat with fabric commissioned from a historic mill in Northern Italy. The breathtaking art deco murals, once hidden behind layers of modern whitewash, have been lovingly brought back to life, shimmering once more in their original gold-leaf splendour.</p>
          <p>From the brass ticketing booths to the opulent chandelier that crowns the main auditorium, every detail has been resurrected to transport you back to the golden age of Hollywood.</p>
        </div>
        <div class="legacy-image-wrapper">
          <img src="https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?q=80&w=1200&auto=format&fit=crop" alt="Restored Cinema Interior" class="legacy-image bloom">
        </div>
      </div>
    </section>

    <!-- Stats Showcase -->
    <section class="stats-showcase" id="statsBox">
      <div class="stat-card reveal" style="animation-delay: 0.1s;">
        <div class="stat-num" id="statFilms">0</div>
        <div class="stat-title">Celluloid Dreams Screened</div>
      </div>
      <div class="stat-card reveal" style="animation-delay: 0.3s;">
        <div class="stat-num" id="statSeats">0</div>
        <div class="stat-title">Patrons Captivated</div>
      </div>
      <div class="stat-card reveal" style="animation-delay: 0.5s;">
        <div class="stat-num" id="statYears">0</div>
        <div class="stat-title">Years of Cinematic History</div>
      </div>
    </section>

    <!-- The Projection Room -->
    <section class="legacy-section" style="background: var(--bg-deep); border: none;">
      <div class="legacy-grid reveal">
        <div class="legacy-image-wrapper">
          <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=1200&auto=format&fit=crop" alt="Twin 35mm Projectors" class="legacy-image bloom" style="filter: sepia(0.6) grayscale(0.5) contrast(1.2);">
        </div>
        <div class="legacy-content">
          <h2>The Heart of the Hall</h2>
          <p>But the true heart of our restoration was the projection hall. While we possess state-of-the-art 4K laser projection for contemporary masterpieces, we have proudly preserved and serviced our twin 35mm carbon arc projectors.</p>
          <p>There is a texture, a warmth, and a soul in film that digital simply cannot replicate - a grain that breathes with every passing second. For special retrospectives and classic re-releases, we still project exactly as the directors originally intended: on glorious, humming 35mm celluloid.</p>
          <a href="movies.php" class="btn-primary" style="margin-top: 30px;">Discover the Program</a>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer>
      <img src="assets/images/logo.svg?v=5" alt="LUMIÈRE" class="logo-img">
      <p style="font-family: var(--font-calligraphy); font-size: 1.8rem; color: var(--gold); margin: 20px 0;">Where every frame tells a story.</p>
      <div class="footer-links" style="margin-top: 25px;">
        <a href="movies.php">Now Showing</a>
        <a href="about.php" style="color: var(--sunset-coral);">The Cinema</a>
        <a href="dashboard_user.php">Account</a>
        <a href="dashboard_admin.php">Staff Area</a>
      </div>
      <p style="margin-top:40px; font-size:0.9rem; opacity:0.3; font-family: var(--font-accent); letter-spacing: 0.1em;">© 2026 LUMIÈRE Cinemas. All rights reserved.</p>
    </footer>

  </div>

  <script src="js/main.js?v=5"></script>
  <script>
    // Stats Counter Animation
    function animateCounter(el, target, dur) {
      if (!el) return;
      let start = 0;
      const step = target / ((dur / 1000) * 60);
      function tick() {
        start += step;
        if (start >= target) { 
          el.textContent = target.toLocaleString() + "+"; 
          return; 
        }
        el.textContent = Math.floor(start).toLocaleString();
        requestAnimationFrame(tick);
      }
      tick();
    }

    const obs = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          animateCounter(document.getElementById('statFilms'), 14500, 2500);
          animateCounter(document.getElementById('statSeats'), 342000, 3000);
          animateCounter(document.getElementById('statYears'), 104, 2000);
          obs.disconnect();
        }
      });
    }, { threshold: 0.5 });
    
    setTimeout(() => {
      const statsBox = document.getElementById('statsBox');
      if (statsBox) obs.observe(statsBox);
    }, 1000);
  </script>
</body>
</html>
