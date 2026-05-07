<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LUMIÈRE - Nosferatu</title>
  <meta name="description" content="Nosferatu - Robert Eggers' gothic reimagining of the classic vampire tale. Now showing at LUMIÈRE.">
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
      <img src="assets/images/poster-nosferatu.png?v=1" alt="" class="bg-img kb-zoom">
      <div class="detail-content fade-up">
        <div class="detail-tagline" data-delay="100">A Symphony of Horror</div>
        <h1 class="detail-title">Nosferatu</h1>
        <div class="detail-meta"><span>2024</span><span>132 Minutes</span><span>Robert Eggers</span><span>R</span></div>
        <div class="divider" style="margin:20px 0;"></div>
      </div>
    </header>
    <section class="editorial-section">
      <div class="editorial-flex">
        <div class="editorial-left fade-up">
          <h2 style="margin-bottom:25px;">The Tale</h2>
          <p class="detail-synopsis">A gothic tale of obsession between a haunted young woman in 19th century Germany and the ancient Transylvanian vampire who stalks her, bringing untold horror with him. Robert Eggers' reimagining of F.W. Murnau's 1922 silent masterpiece plunges audiences into a world of shadow and dread, where Count Orlok's sinister presence consumes everything it touches. As the vampire's dark influence spreads like a plague across the land, a desperate circle of allies must confront an ancient evil that defies comprehension-one that feeds not just on blood, but on desire, madness, and the very soul of its victims.</p>
          <div class="reviews-section">
            <h2 style="margin-bottom:25px;">Critic Reviews</h2>
            <div class="reviews-grid">
              <div class="review-card"><div class="ink-stars">★★★★★</div><p class="review-text">"Eggers has created the most terrifying vampire film in decades. Every frame is a meticulously composed nightmare."</p><span class="review-author">- IndieWire</span></div>
              <div class="review-card"><div class="ink-stars">★★★★★</div><p class="review-text">"Bill Skarsgård's Count Orlok is the stuff of genuine nightmares-inhuman, repulsive, and utterly mesmerizing."</p><span class="review-author">- The A.V. Club</span></div>
              <div class="review-card"><div class="ink-stars">★★★★☆</div><p class="review-text">"A sumptuous feast of gothic horror. Lily-Rose Depp delivers a haunting performance of possession and desire."</p><span class="review-author">- Total Film</span></div>
            </div>
          </div>
        </div>
        <div class="editorial-right fade-right">
          <h2 style="font-size:1.5rem; margin-bottom:25px;">Starring</h2>
          <ul class="cast-list">
            <li><span class="cast-name">Bill Skarsgård</span><span class="cast-role">Count Orlok</span></li>
            <li><span class="cast-name">Lily-Rose Depp</span><span class="cast-role">Ellen Hutter</span></li>
            <li><span class="cast-name">Nicholas Hoult</span><span class="cast-role">Thomas Hutter</span></li>
            <li><span class="cast-name">Willem Dafoe</span><span class="cast-role">Prof. Albin Eberhart Von Franz</span></li>
            <li><span class="cast-name">Aaron Taylor-Johnson</span><span class="cast-role">Friedrich Harding</span></li>
          </ul>
          <div style="margin-top:40px;">
            <h3 style="font-size:1.5rem; margin-bottom:15px;">Purchase Admission</h3>
            <div class="ticket-tiers">
              <div class="ticket-tier selected" onclick="selectTier(this)"><div><div class="tier-name">Stalls</div><div style="font-size:0.9rem; color:var(--mocha); font-style:italic;">Main level</div></div><div class="tier-price">£12.00</div></div>
              <div class="ticket-tier" onclick="selectTier(this)"><div><div class="tier-name">Circle</div><div style="font-size:0.9rem; color:var(--mocha); font-style:italic;">Balcony view</div></div><div class="tier-price">£18.00</div></div>
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
