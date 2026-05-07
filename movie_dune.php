<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LUMIÈRE - Dune: Part Two</title>
  <meta name="description" content="Dune: Part Two - Paul Atreides unites with Chani and the Fremen on Arrakis. Now showing at LUMIÈRE.">
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
      <img src="assets/images/poster-dune.png?v=1" alt="" class="bg-img kb-zoom">
      <div class="detail-content fade-up">
        <div class="detail-tagline" data-delay="100">Beyond Fear, Fate Awaits</div>
        <h1 class="detail-title">Dune: Part Two</h1>
        <div class="detail-meta"><span>2024</span><span>166 Minutes</span><span>Denis Villeneuve</span><span>PG-13</span></div>
        <div class="divider" style="margin:20px 0;"></div>
      </div>
    </header>
    <section class="editorial-section">
      <div class="editorial-flex">
        <div class="editorial-left fade-up">
          <h2 style="margin-bottom:25px;">The Tale</h2>
          <p class="detail-synopsis">Paul Atreides unites with Chani and the Fremen while on a warpath of revenge against the conspirators who destroyed his family. Facing a choice between the love of his life and the fate of the known universe, he endeavors to prevent a terrible future only he can foresee. As Paul embraces his destiny among the desert warriors, he must navigate treacherous political alliances, confront the messianic prophecy that both empowers and haunts him, and prepare for an epic confrontation with the ruthless House Harkonnen that will determine the future of Arrakis and the entire Imperium.</p>
          <div class="reviews-section">
            <h2 style="margin-bottom:25px;">Critic Reviews</h2>
            <div class="reviews-grid">
              <div class="review-card"><div class="ink-stars">★★★★★</div><p class="review-text">"A visceral, awe-inspiring spectacle. Villeneuve has crafted the definitive science fiction epic of our generation."</p><span class="review-author">- Variety</span></div>
              <div class="review-card"><div class="ink-stars">★★★★★</div><p class="review-text">"The sandworm sequences alone are worth the price of admission. Zimmer's score shakes you to your bones."</p><span class="review-author">- Rolling Stone</span></div>
              <div class="review-card"><div class="ink-stars">★★★★☆</div><p class="review-text">"Timothée Chalamet fully inhabits Paul's transformation from reluctant heir to messianic war leader."</p><span class="review-author">- The Telegraph</span></div>
            </div>
          </div>
        </div>
        <div class="editorial-right fade-right">
          <h2 style="font-size:1.5rem; margin-bottom:25px;">Starring</h2>
          <ul class="cast-list">
            <li><span class="cast-name">Timothée Chalamet</span><span class="cast-role">Paul Atreides</span></li>
            <li><span class="cast-name">Zendaya</span><span class="cast-role">Chani</span></li>
            <li><span class="cast-name">Austin Butler</span><span class="cast-role">Feyd-Rautha</span></li>
            <li><span class="cast-name">Rebecca Ferguson</span><span class="cast-role">Lady Jessica</span></li>
            <li><span class="cast-name">Javier Bardem</span><span class="cast-role">Stilgar</span></li>
          </ul>
          <div style="margin-top:40px;">
            <h3 style="font-size:1.5rem; margin-bottom:15px;">Purchase Admission</h3>
            <div class="ticket-tiers">
              <div class="ticket-tier selected" onclick="selectTier(this)"><div><div class="tier-name">Stalls</div><div style="font-size:0.9rem; color:var(--mocha); font-style:italic;">Main level</div></div><div class="tier-price">£15.00</div></div>
              <div class="ticket-tier" onclick="selectTier(this)"><div><div class="tier-name">Circle</div><div style="font-size:0.9rem; color:var(--mocha); font-style:italic;">Balcony view</div></div><div class="tier-price">£22.00</div></div>
              <div class="ticket-tier" onclick="selectTier(this)"><div><div class="tier-name">Royal Box</div><div style="font-size:0.9rem; color:var(--mocha); font-style:italic;">Private booth</div></div><div class="tier-price">£50.00</div></div>
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
