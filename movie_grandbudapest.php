<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LUMIÈRE - The Grand Budapest Hotel</title>
  <meta name="description" content="The Grand Budapest Hotel - A Wes Anderson Picture. Now showing at LUMIÈRE.">
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
      <img src="assets/images/poster-grandbudapest.png?v=1" alt="" class="bg-img kb-zoom">
      <div class="detail-content fade-up">
        <div class="detail-tagline" data-delay="100">A Wes Anderson Picture</div>
        <h1 class="detail-title">The Grand Budapest Hotel</h1>
        <div class="detail-meta">
          <span>2014</span><span>99 Minutes</span><span>Wes Anderson</span><span>15</span>
        </div>
        <div class="divider" style="margin:20px 0;"></div>
      </div>
    </header>

    <section class="editorial-section">
      <div class="editorial-flex">
        <div class="editorial-left fade-up">
          <h2 style="margin-bottom:25px;">The Tale</h2>
          <p class="detail-synopsis">
            The Grand Budapest Hotel recounts the adventures of Gustave H, a legendary concierge at a famous European hotel between the first and second World Wars, and Zero Moustafa, the lobby boy who becomes his most trusted friend. The story involves the theft and recovery of a priceless Renaissance painting and the battle for an enormous family fortune-all against the back-drop of a suddenly and dramatically changing Continent.
          </p>

          <div class="reviews-section">
            <h2 style="margin-bottom:25px;">Critic Reviews</h2>
            <div class="reviews-grid">
              <div class="review-card">
                <div class="ink-stars">★★★★★</div>
                <p class="review-text">"An exhilarating, deeply humorous and occasionally sorrowful marvel. Anderson at his very best."</p>
                <span class="review-author">- The Times</span>
              </div>
              <div class="review-card">
                <div class="ink-stars">★★★★★</div>
                <p class="review-text">"A meticulously crafted dollhouse of a movie that houses a surprisingly big beating heart."</p>
                <span class="review-author">- Cinema Review</span>
              </div>
            </div>
          </div>
        </div>

        <div class="editorial-right fade-right">
          <h2 style="font-size:1.5rem; margin-bottom:25px;">Starring</h2>
          <ul class="cast-list">
            <li><span class="cast-name">Ralph Fiennes</span><span class="cast-role">M. Gustave</span></li>
            <li><span class="cast-name">Tony Revolori</span><span class="cast-role">Zero Moustafa</span></li>
            <li><span class="cast-name">Saoirse Ronan</span><span class="cast-role">Agatha</span></li>
            <li><span class="cast-name">Willem Dafoe</span><span class="cast-role">Jopling</span></li>
            <li><span class="cast-name">Jeff Goldblum</span><span class="cast-role">Deputy Kovacs</span></li>
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
