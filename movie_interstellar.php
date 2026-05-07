<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LUMIÈRE - Interstellar</title>
  <meta name="description" content="Interstellar - Christopher Nolan's epic journey through space and time. Restored screening at LUMIÈRE.">
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
    <div class="nav-links"><a href="index.php" class="nav-link">Home</a>
      <a href="movies.php" class="nav-link">Now Showing</a><a href="history.php" class="nav-link">My Tickets</a><a href="about.php" class="nav-link">The Cinema</a></div>
  </nav>
  <div class="page-wrapper">
    <header class="detail-hero">
      <img src="assets/images/poster-interstellar.png?v=1" alt="" class="bg-img kb-zoom">
      <div class="detail-content fade-up">
        <div class="detail-tagline" data-delay="100">The End of Earth will not be the End of Us</div>
        <h1 class="detail-title">Interstellar</h1>
        <div class="detail-meta"><span>2014</span><span>169 Minutes</span><span>Christopher Nolan</span><span>PG-13</span></div>
        <div class="divider" style="margin:20px 0;"></div>
      </div>
    </header>
    <section class="editorial-section">
      <div class="editorial-flex">
        <div class="editorial-left fade-up">
          <h2 style="margin-bottom:25px;">The Tale</h2>
          <p class="detail-synopsis">In Earth's future, a global crop blight and second Dust Bowl are slowly rendering the planet uninhabitable. Professor Brand, a brilliant NASA physicist, is working on plans to save mankind by transporting Earth's population to a new home via a wormhole. Former NASA pilot Cooper is recruited to lead a daring mission through the wormhole alongside a team of researchers to find a habitable planet. As they traverse the cosmos, experiencing the mind-bending effects of relativity-where hours on one planet equal years back on Earth-Cooper must weigh his duty to humanity against the aching desire to return to his children, who are growing up without him. The film is a breathtaking meditation on love, sacrifice, and the indomitable human spirit.</p>
          <div class="reviews-section">
            <h2 style="margin-bottom:25px;">Critic Reviews</h2>
            <div class="reviews-grid">
              <div class="review-card"><div class="ink-stars">★★★★★</div><p class="review-text">"A masterwork of science fiction cinema. Nolan has crafted something that sits alongside 2001: A Space Odyssey in the pantheon."</p><span class="review-author">- The Observer</span></div>
              <div class="review-card"><div class="ink-stars">★★★★★</div><p class="review-text">"The docking sequence is the most tense thing I have ever experienced in a cinema. Hans Zimmer's organ score is transcendent."</p><span class="review-author">- Empire Magazine</span></div>
              <div class="review-card"><div class="ink-stars">★★★★☆</div><p class="review-text">"McConaughey's performance is raw and deeply human. The bookshelf scene will leave you in pieces."</p><span class="review-author">- Time Out</span></div>
            </div>
          </div>
        </div>
        <div class="editorial-right fade-right">
          <h2 style="font-size:1.5rem; margin-bottom:25px;">Starring</h2>
          <ul class="cast-list">
            <li><span class="cast-name">Matthew McConaughey</span><span class="cast-role">Cooper</span></li>
            <li><span class="cast-name">Anne Hathaway</span><span class="cast-role">Dr. Amelia Brand</span></li>
            <li><span class="cast-name">Jessica Chastain</span><span class="cast-role">Murph (adult)</span></li>
            <li><span class="cast-name">Michael Caine</span><span class="cast-role">Professor Brand</span></li>
            <li><span class="cast-name">Matt Damon</span><span class="cast-role">Dr. Mann</span></li>
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
