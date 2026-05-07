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
</head>
<body>
  
  <div class="film-grain"></div>
  <div class="page-transition active" id="pageTransition"><span class="trans-logo">LUMIÈRE</span></div>
  <nav class="lumiere-nav">
    <a href="movies.php" class="lumiere-logo"><img src="assets/images/logo.svg?v=5" alt="LUMIÈRE"></a>
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
      <h2 class="fade-up" style="background:var(--bg-dark); padding:12px 30px; z-index:10; border-radius:6px; text-align:center; border:1px solid rgba(212,168,83,0.15);">Your Cinematographic Journey</h2>
      <div class="timeline-item reveal" data-delay="200">
        <div class="stub-card">
          <div class="stub-card-inner">
            <div class="stub-front">
              <span class="stub-serial">NO. 88241-B</span>
              <div class="stub-theatre">Lumière Cinema - Paris</div>
              <h3 class="stub-title">Oppenheimer</h3>
              <div class="stub-meta">22 MAR 2026 • ADMIT TWO</div>
              <div style="margin-top:10px; font-weight:700; color:var(--retro-red); border:1px solid var(--retro-red); padding:2px 8px; font-size:0.75rem; border-radius:3px;">ORCHESTRA D4, D5</div>
            </div>
            <div class="stub-back">
              <div class="stub-qr-mock"></div>
              <h3>Digital Pass</h3>
              <p style="font-size:0.8rem; font-style:italic; opacity:0.6;">Flip to show at the gate</p>
            </div>
          </div>
        </div>
      </div>

      <div class="timeline-item reveal" data-delay="400">
        <div class="stub-card">
          <div class="stub-card-inner">
            <div class="stub-front" style="opacity:0.7; filter:grayscale(20%) sepia(30%);">
              <span class="stub-serial">NO. 77312-A</span>
              <div class="stub-theatre">Lumière Cinema - Paris</div>
              <h3 class="stub-title">Dune: Part Two</h3>
              <div class="stub-meta">15 MAR 2026 • ADMIT ONE</div>
              <div style="position:absolute; bottom:20px; right:20px; color:var(--sunset-coral); font-weight:900; font-size:1.2rem; transform:rotate(-15deg); border:3px solid var(--sunset-coral); padding:5px 15px; text-transform:uppercase;">USED</div>
            </div>
            <div class="stub-back">
              <div class="stub-qr-mock"></div>
              <h3>Archive Entry</h3>
              <p style="font-size:0.8rem; font-style:italic; opacity:0.6;">Verified 15 Mar 2026</p>
            </div>
          </div>
        </div>
      </div>

      <div class="timeline-item reveal" data-delay="600">
        <div class="stub-card">
          <div class="stub-card-inner">
            <div class="stub-front" style="opacity:0.6; filter:grayscale(40%) sepia(40%);">
              <span class="stub-serial">NO. 45109-C</span>
              <div class="stub-theatre">Lumière Cinema - Paris</div>
              <h3 class="stub-title">Interstellar</h3>
              <div class="stub-meta">02 FEB 2026 • ADMIT TWO</div>
              <div style="position:absolute; bottom:20px; right:20px; color:var(--sunset-coral); font-weight:900; font-size:1.2rem; transform:rotate(-15deg); border:3px solid var(--sunset-coral); padding:5px 15px; text-transform:uppercase;">USED</div>
            </div>
            <div class="stub-back">
              <div class="stub-qr-mock"></div>
              <h3>Archive Entry</h3>
              <p style="font-size:0.8rem; font-style:italic; opacity:0.6;">Verified 02 Feb 2026</p>
            </div>
          </div>
        </div>
      </div>
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

  <script src="js/main.js?v=5"></script>
</body>
</html>
