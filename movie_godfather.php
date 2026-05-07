<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LUMIÈRE - The Godfather</title>
  <meta name="description" content="The Godfather - An Offer You Can't Refuse. Now showing at LUMIÈRE.">
  <link rel="stylesheet" href="css/base.css?v=5">
    <link rel="stylesheet" href="css/pages/movie-details.css?v=5">
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
      <a href="history.php" class="nav-link">My Tickets</a>
      <a href="about.php" class="nav-link">The Cinema</a>
    </div>
  </nav>

  <div class="page-wrapper">
    <header class="detail-hero">
      <img src="assets/images/poster-godfather.png?v=1" alt="" class="bg-img kb-zoom">
      <div class="detail-content fade-up">
        <div class="detail-tagline" data-delay="100">An Offer You Can't Refuse</div>
        <h1 class="detail-title">The Godfather</h1>
        <div class="detail-meta">
          <span>1972</span><span>175 Minutes</span><span>Francis Ford Coppola</span><span>18</span>
        </div>
        <div class="divider" style="margin:20px 0;"></div>
      </div>
    </header>

    <section class="editorial-section">
      <div class="editorial-flex">
        <div class="editorial-left fade-up">
          <h2 style="margin-bottom:25px;">The Tale</h2>
          <p class="detail-synopsis">
            The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son. Widely regarded as one of the greatest films of all time, this mob drama focuses on the powerful Italian-American crime family of Don Vito Corleone. As Michael Corleone is drawn into the inevitable cycle of violence and betrayal, he must navigate the complex codes of loyalty and the high cost of power, transforming from a war hero to a ruthless mafia don.
          </p>

          <div class="reviews-section">
            <h2 style="margin-bottom:25px;">Critic Reviews</h2>
            <div class="reviews-grid">
              <div class="review-card">
                <div class="ink-stars">★★★★★</div>
                <p class="review-text">"One of the most brutally moving portraits of American life ever made. A flawless masterpiece."</p>
                <span class="review-author">- The New Yorker</span>
              </div>
              <div class="review-card">
                <div class="ink-stars">★★★★★</div>
                <p class="review-text">"Its shadowy cinematography and unforgettable score make it an immersive, tragic epic of power and family."</p>
                <span class="review-author">- Cinema Classics</span>
              </div>
            </div>
          </div>
        </div>

        <div class="editorial-right fade-right">
          <h2 style="font-size:1.5rem; margin-bottom:25px;">Starring</h2>
          <ul class="cast-list">
            <li><span class="cast-name">Marlon Brando</span><span class="cast-role">Don Vito Corleone</span></li>
            <li><span class="cast-name">Al Pacino</span><span class="cast-role">Michael Corleone</span></li>
            <li><span class="cast-name">James Caan</span><span class="cast-role">Sonny Corleone</span></li>
            <li><span class="cast-name">Diane Keaton</span><span class="cast-role">Kay Adams</span></li>
            <li><span class="cast-name">Robert Duvall</span><span class="cast-role">Tom Hagen</span></li>
          </ul>
          <div style="margin-top:40px;">
            <h3 style="font-size:1.5rem; margin-bottom:15px;">Purchase Admission</h3>
            <div class="ticket-tiers">
              <div class="ticket-tier selected" onclick="selectTier(this)"><div><div class="tier-name">Stalls</div><div style="font-size:0.9rem; color:var(--mocha); font-style:italic;">Main level</div></div><div class="tier-price">£12.00</div></div>
              <div class="ticket-tier" onclick="selectTier(this)"><div><div class="tier-name">Circle</div><div style="font-size:0.9rem; color:var(--mocha); font-style:italic;">Balcony view</div></div><div class="tier-price">£18.00</div></div>
              <div class="ticket-tier" onclick="selectTier(this)"><div><div class="tier-name">Royal Box</div><div style="font-size:0.9rem; color:var(--mocha); font-style:italic;">Private booth</div></div><div class="tier-price">£45.00</div></div>
            </div>
            <button class="btn-coral" style="width:100%; margin-top:25px;" onclick="triggerPageTransition('booking.php')">Select Seats</button>
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
  <script>function selectTier(el){document.querySelectorAll('.ticket-tier').forEach(t=>t.classList.remove('selected'));el.classList.add('selected');}</script>
</body>
</html>
