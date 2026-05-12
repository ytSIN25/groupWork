<?php
require_once 'config.php';
$mid = $_GET['movie_id'] ?? 1;
$stmt = $conn->prepare("SELECT * FROM movies WHERE movie_id = ?");
$stmt->bind_param("i", $mid);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$movie) {
    header('Location: movies.php');
    exit();
}

// Login state
$isLoggedIn  = isset($_SESSION['user_id']);
$sessionUser = $_SESSION['user_id'] ?? null;

// ---------- Fetch user seating preference ----------
$user_pref_tier = 'Stalls';
if ($isLoggedIn) {
    $stmt_p = $conn->prepare("SELECT preferred_seating FROM user_preferences WHERE user_id = ?");
    $stmt_p->bind_param("i", $sessionUser);
    $stmt_p->execute();
    $pref_res = $stmt_p->get_result();
    if ($pref = $pref_res->fetch_assoc()) {
        $ps = $pref['preferred_seating'];
        if (strpos($ps, 'Circle') !== false)     $user_pref_tier = 'Circle';
        elseif (strpos($ps, 'Royal') !== false)  $user_pref_tier = 'Royal Box';
    }
    $stmt_p->close();
}

// ---------- Critic reviews: hardcoded pools by genre ----------
$critic_pool = [
    'Drama' => [
        ['stars' => 5, 'text' => 'A masterwork of restraint and raw emotion. Every frame earns its place on the screen.', 'source' => 'The Luminal Daily'],
        ['stars' => 5, 'text' => 'Devastating, humane, and completely unforgettable. The finest drama of the year by a considerable margin.', 'source' => 'Parisian Film Review'],
        ['stars' => 4, 'text' => 'Quietly shattering. The performances are so honest they feel stolen from real life.', 'source' => 'Ciné Monde'],
        ['stars' => 5, 'text' => 'A film that trusts its audience to feel without being told how. Rare and precious.', 'source' => 'The Criterion Observer'],
    ],
    'Thriller' => [
        ['stars' => 5, 'text' => 'A vice-grip of tension from the first scene to the last. You will not breathe.', 'source' => 'The Luminal Daily'],
        ['stars' => 5, 'text' => 'Relentlessly clever plotting wrapped in an atmosphere thick enough to choke on.', 'source' => 'Noir & Beyond'],
        ['stars' => 4, 'text' => 'The kind of thriller that makes you question everyone on screen - and in the seat beside you.', 'source' => 'Parisian Film Review'],
        ['stars' => 5, 'text' => 'Meticulous, menacing, and utterly gripping. A genre high-water mark.', 'source' => 'The Celluloid Standard'],
    ],
    'Horror' => [
        ['stars' => 5, 'text' => 'Deeply unsettling in the best possible way. It lodges in the mind long after the credits roll.', 'source' => 'The Luminal Daily'],
        ['stars' => 4, 'text' => 'Fear crafted with surgical precision. Every sound cue, every shadow is exactly where it should be.', 'source' => 'Dread Quarterly'],
        ['stars' => 5, 'text' => 'Genuinely terrifying and genuinely thoughtful - a combination rarer than it should be.', 'source' => 'Parisian Film Review'],
        ['stars' => 4, 'text' => 'The atmosphere alone could win awards. A modern horror landmark.', 'source' => 'The Midnight Screen'],
    ],
    'Romance' => [
        ['stars' => 5, 'text' => 'Tender, aching, and luminously performed. A love story that reminds you why the genre exists.', 'source' => 'The Luminal Daily'],
        ['stars' => 5, 'text' => 'The chemistry between the leads is practically combustible. You leave the cinema slightly in love yourself.', 'source' => 'Parisian Film Review'],
        ['stars' => 4, 'text' => 'A romance with the courage to be heartbreaking as well as beautiful.', 'source' => 'La Belle Époque Review'],
        ['stars' => 5, 'text' => 'Swooning, bittersweet, and shot through with longing. Pure cinema.', 'source' => 'Ciné Monde'],
    ],
    'Comedy' => [
        ['stars' => 5, 'text' => 'Wickedly sharp and timed to perfection. Every laugh is earned, none are cheap.', 'source' => 'The Luminal Daily'],
        ['stars' => 4, 'text' => 'Riotously funny and, somehow, quietly moving too. A genuinely difficult double act pulled off with ease.', 'source' => 'Parisian Film Review'],
        ['stars' => 5, 'text' => 'The best comedy in years. It understands that real wit comes from character, not from jokes.', 'source' => 'The Criterion Observer'],
        ['stars' => 4, 'text' => 'Breezy, warm, and surprisingly poignant. It sneaks up on you.', 'source' => 'Ciné Monde'],
    ],
    'Action' => [
        ['stars' => 5, 'text' => 'Kinetic, visceral, and shot with the kind of confidence that turns spectacle into art.', 'source' => 'The Luminal Daily'],
        ['stars' => 4, 'text' => 'Breathless and brilliantly choreographed. Action filmmaking at its most exhilarating.', 'source' => 'Parisian Film Review'],
        ['stars' => 5, 'text' => 'A propulsive triumph. It raises the bar for the genre and then clears it with room to spare.', 'source' => 'The Celluloid Standard'],
        ['stars' => 4, 'text' => 'Spectacular set-pieces grounded by characters you actually care about. The way it should be done.', 'source' => 'Ciné Monde'],
    ],
    'Sci-Fi' => [
        ['stars' => 5, 'text' => 'Visionary world-building paired with ideas that genuinely haunt. The best science fiction asks questions it cannot answer.', 'source' => 'The Luminal Daily'],
        ['stars' => 5, 'text' => 'Audacious, immersive, and intellectually fearless. A landmark of speculative cinema.', 'source' => 'Parisian Film Review'],
        ['stars' => 4, 'text' => 'Rare science fiction that treats its audience as intelligent adults. Astonishing in scope and in feeling.', 'source' => 'The Criterion Observer'],
        ['stars' => 5, 'text' => 'The future, rendered with such conviction it feels like a memory.', 'source' => 'Orbital Review'],
    ],
    'Animation' => [
        ['stars' => 5, 'text' => 'A triumph of imagination. It expands what the form can do and will delight every generation watching.', 'source' => 'The Luminal Daily'],
        ['stars' => 5, 'text' => 'Visually extraordinary and emotionally devastating. Animation has never felt more essential.', 'source' => 'Parisian Film Review'],
        ['stars' => 4, 'text' => 'Bursting with invention and warmth. The sort of film that grows more beautiful on every viewing.', 'source' => 'Ciné Monde'],
        ['stars' => 5, 'text' => 'A film for everyone and about everyone. Joyful, sorrowful, and utterly alive.', 'source' => 'The Criterion Observer'],
    ],
    'default' => [
        ['stars' => 5, 'text' => 'A stunning achievement in cinema. This is exactly why we go to the movies.', 'source' => 'The Luminal Daily'],
        ['stars' => 5, 'text' => 'Immersive, beautiful, and profoundly moving. A must-watch for any cinephile.', 'source' => 'Parisian Film Review'],
        ['stars' => 4, 'text' => 'Impeccably crafted and utterly absorbing. A film made with real conviction.', 'source' => 'The Criterion Observer'],
        ['stars' => 5, 'text' => 'Cinema at its most powerful - challenging, resonant, and impossible to shake.', 'source' => 'Ciné Monde'],
    ],
];

$genre_key = 'default';
foreach (array_keys($critic_pool) as $key) {
    if (stripos($movie['genre'], $key) !== false) { $genre_key = $key; break; }
}
$pool   = $critic_pool[$genre_key];
$offset = $movie['movie_id'] % count($pool);
$critic_reviews = [
    $pool[$offset % count($pool)],
    $pool[($offset + 1) % count($pool)],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LUMIÈRE - <?= htmlspecialchars($movie['movie_name']) ?></title>
  <meta name="description" content="<?= htmlspecialchars(substr($movie['description'], 0, 150)) ?>... Now showing at LUMIÈRE.">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/pages/movie-details.css">
  <link rel="stylesheet" href="css/pages/booking.css">
  <link rel="stylesheet" href="css/pages/footer.css">
  <link rel="stylesheet" href="css/global.css">
  <link rel="icon" type="image/png" href="assets/images/favicon.png">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .lumiere-swal-popup {
      background: rgb(26, 21, 32);
      border-radius: 6px;
      font-family: inherit;
      padding: 15px 40px 25px;
      max-width: 450px ;
      box-shadow: 0 32px 80px rgba(0, 0, 0, 0.7) ;
      position: relative;
      overflow: hidden;
    }

      .lumiere-swal-popup::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, var(--gold), var(--sunset-coral));
    }

    .lumiere-swal-title {
      font-family: var(--font-display);
      font-size: 1.8rem;
      color: var(--gold);
      font-style: italic;
      font-weight: 400;
    }

    .lumiere-swal-html {
      color: #ddd;
      font-size: 1rem;
      line-height: 1.5;
      opacity: 0.8;
    }

    .lumiere-swal-actions {
      justify-content: flex-start ;
      margin-top: 28px ;
      padding: 0 ;
      gap: 10px ;
    }

    .lumiere-swal-confirm {
      padding: 12px 25px;
      border-radius: 6px;
      font-family: var(--font-accent);
      text-transform: uppercase;
      letter-spacing: 1.5px;
      font-size: 0.85rem;
      cursor: pointer;
      transition: all 0.3s ease;
      min-width: 120px;
      background: var(--sunset-coral);
      border: none;
      color: white;
    }
    .lumiere-swal-confirm:hover  { background: #f65d3f; box-shadow: 0 0 20px rgba(239, 125, 99, 0.4) !important; transform: translateY(-2px); }
    .lumiere-swal-confirm:focus  { box-shadow: none ; }

    .lumiere-swal-cancel {
      padding: 12px 25px;
      border-radius: 6px;
      font-family: var(--font-accent);
      text-transform: uppercase;
      letter-spacing: 1.5px;
      font-size: 0.85rem;
      cursor: pointer;
      transition: all 0.3s ease;
      min-width: 120px;
      background: transparent;
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
    }
    .lumiere-swal-cancel:hover  { background: rgba(255, 255, 255, 0.05); border-color: white; }
    .lumiere-swal-cancel:focus  { box-shadow: none ; }

    .swal2-backdrop-show {
      background: rgba(0, 0, 0, 0.82) ;
      backdrop-filter: blur(3px) ;
    }
  </style>
</head>
<body>
  <div class="film-grain"></div>
  <div class="page-transition active" id="pageTransition"><span class="trans-logo">LUMIÈRE</span></div>

  <nav class="lumiere-nav">
    <a href="movies.php" class="lumiere-logo"><img src="assets/images/logo.svg" alt="LUMIÈRE"></a>
    <button class="hamburger" id="hamburgerBtn" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
    </button>
    <div class="nav-links">
      <a href="index.php"          class="nav-link">Home</a>
      <a href="movies.php"         class="nav-link">Now Showing</a>
      <a href="history.php"        class="nav-link">My Tickets</a>
      <a href="dashboard_user.php" class="nav-link">Account</a>
      <a href="about.php"          class="nav-link">The Cinema</a>
    </div>
  </nav>

  <div class="page-wrapper">
    <header class="detail-hero">
      <img src="<?= htmlspecialchars($movie['poster_path']) ?>" alt="" class="bg-img kb-zoom">
      <div class="detail-content fade-up">
        <div class="detail-tagline" data-delay="100">LUMIÈRE Selection</div>
        <h1 class="detail-title"><?= htmlspecialchars($movie['movie_name']) ?></h1>
        <div class="detail-meta">
          <span><?= $movie['release_year'] ?></span>
          <span><?= $movie['duration'] ?> Minutes</span>
          <span><?= htmlspecialchars($movie['director']) ?></span>
          <span><?= $movie['genre'] ?></span>
        </div>
        <div class="divider" style="margin:20px 0;"></div>
      </div>
    </header>

    <section class="editorial-section">
      <div class="editorial-flex">

        <div class="editorial-tale fade-up">
          <h2 style="margin-bottom:25px;">The Tale</h2>
          <p class="detail-synopsis">
            <?= nl2br(htmlspecialchars($movie['description'])) ?>
          </p>
        </div>

        <div class="editorial-right fade-right">
          <h2 style="font-size:1.5rem; margin-bottom:25px;">Starring</h2>
          <ul class="cast-list">
            <?php foreach (explode(',', $movie['starring']) as $star): ?>
            <li><span class="cast-name"><?= htmlspecialchars(trim($star)) ?></span></li>
            <?php endforeach; ?>
          </ul>
          <div style="margin-top:40px;">
            <h3 style="font-size:1.5rem; margin-bottom:15px;">Purchase Admission</h3>
            <div class="ticket-tiers" id="tierSelector">
              <div class="ticket-tier <?= ($user_pref_tier === 'Stalls')    ? 'selected' : '' ?>" data-tier="Stalls"    onclick="selectTier(this)">
                <div><div class="tier-name">Stalls</div><div style="font-size:0.9rem;color:var(--mocha);font-style:italic;">Main level</div></div>
                <div class="tier-price">€<?= number_format(LUMIERE_BASE_PRICE, 2) ?></div>
              </div>
              <div class="ticket-tier <?= ($user_pref_tier === 'Circle')    ? 'selected' : '' ?>" data-tier="Circle"    onclick="selectTier(this)">
                <div><div class="tier-name">Circle</div><div style="font-size:0.9rem;color:var(--mocha);font-style:italic;">Balcony view</div></div>
                <div class="tier-price">€<?= number_format(LUMIERE_BASE_PRICE * 1.5, 2) ?></div>
              </div>
              <div class="ticket-tier <?= ($user_pref_tier === 'Royal Box') ? 'selected' : '' ?>" data-tier="Royal Box" onclick="selectTier(this)">
                <div><div class="tier-name">Royal Box</div><div style="font-size:0.9rem;color:var(--mocha);font-style:italic;">Private booth</div></div>
                <div class="tier-price">€<?= number_format(LUMIERE_BASE_PRICE * 3, 2) ?></div>
              </div>
            </div>
            <button class="btn-coral" style="width:100%;margin-top:25px;background:linear-gradient(135deg,var(--retro-red),var(--retro-red-glow));" onclick="proceedToBooking()">Select Seats</button>
          </div>
        </div>

        <div class="editorial-reviews fade-up">
          <div class="reviews-section">
            <h2 style="margin-bottom:25px;">Critic Reviews</h2>
            <div class="reviews-grid">
              <?php foreach ($critic_reviews as $cr): ?>
              <div class="review-card">
                <div class="ink-stars"><?= str_repeat('★', $cr['stars']) . str_repeat('☆', 5 - $cr['stars']) ?></div>
                <p class="review-text">"<?= htmlspecialchars($cr['text']) ?>"</p>
                <span class="review-author">- <?= htmlspecialchars($cr['source']) ?></span>
              </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="reviews-section" id="patron-reviews-section">
            <h2 style="margin-bottom:28px;">Patron Reviews</h2>
            <div id="patron-loading">
              <div class="skeleton"></div>
              <div class="skeleton"></div>
            </div>
            <div id="patron-content" style="display:none;"></div>
          </div>
        </div>

      </div>
    </section>

    <footer>
      <img src="assets/images/logo.svg" alt="LUMIÈRE" class="logo-img">
      <p>Where Every Seat Tells a Story.</p>
      <div class="footer-links">
        <a href="movies.php">Now Showing</a>
        <a href="about.php">The Cinema</a>
        <a href="dashboard_user.php">Account</a>
        <a href="dashboard_admin.php">Staff Area</a>
      </div>
      <p style="margin-top:30px;font-size:0.9rem;opacity:0.5;">© 2026 LUMIÈRE Cinemas. All rights reserved.</p>
    </footer>
  </div>

  <script src="js/main.js"></script>
  <script>
    // Movie ID and login state passed from PHP
    const MOVIE_ID     = <?= intval($movie['movie_id']) ?>;
    const IS_LOGGED_IN = <?= $isLoggedIn ? 'true' : 'false' ?>;

    // Tier Selection & Booking
    function selectTier(el) {
      document.querySelectorAll('.ticket-tier').forEach(t => t.classList.remove('selected'));
      el.classList.add('selected');
    }

    function proceedToBooking() {
      const selected = document.querySelector('.ticket-tier.selected');
      const tier = selected ? selected.getAttribute('data-tier') : 'Stalls';
      triggerPageTransition(`booking.php?movie_id=${MOVIE_ID}&tier=${encodeURIComponent(tier)}`);
    }

    function starsHtml(filled, total = 5) {
      let s = '';
      for (let i = 1; i <= total; i++) s += i <= filled ? '★' : '☆';
      return s;
    }

    function avatarHtml(avatar, name) {
      if (avatar) {
        return `<img src="${avatar}" alt="" class="review-avatar">`;
      }
      const initial = (name || '?').charAt(0).toUpperCase();
      return `<div class="review-avatar-placeholder">${initial}</div>`;
    }

    // Patron reviews from API data
    function renderPatronReviews(data) {
      const { reviews, avg_rating, total, distribution, user_review, logged_in, user_id } = data;

      let html = '';

      // Rating summary
      if (total > 0) {
        const fullStars = Math.floor(avg_rating);
        let starsRow = '';
        for (let i = 1; i <= 5; i++) starsRow += i <= fullStars ? '★' : '☆';

        html += `
          <div class="rating-summary">
            <div class="rating-big">
              <div class="big-number">${avg_rating}</div>
              <div class="big-stars">${starsRow}</div>
              <div class="big-count">${total} review${total !== 1 ? 's' : ''}</div>
            </div>
            <div class="star-distribution">`;

        for (let s = 5; s >= 1; s--) {
          const cnt = distribution[s] || 0;
          const pct = total > 0 ? ((cnt / total) * 100).toFixed(1) : 0;
          html += `
              <div class="star-bar-row">
                <span class="bar-label">${s} ★</span>
                <div class="bar-track"><div class="bar-fill" style="width:${pct}%"></div></div>
                <span class="bar-count">${cnt}</span>
              </div>`;
        }

        html += `
            </div>
          </div>`;

        // Review cards
        html += `<div class="reviews-grid">`;
        reviews.forEach(r => {
          const isOwner = logged_in && String(r.user_id) === String(user_id);
          html += `
            <div class="review-card">
              <div class="review-card-header">
                ${avatarHtml(r.avatar, r.name)}
                <div class="review-author-name">${r.name}</div>
                ${isOwner ? `<span class="review-edit-badge">YOURS</span>` : ''}
              </div>
              <div class="ink-stars" style="margin-bottom:10px;">${starsHtml(r.star_num)}</div>
              ${r.content ? `<p class="review-text">"${r.content}"</p>` : ''}
              ${isOwner ? `
              <div class="patron-card-actions">
                <button class="review-delete-btn" onclick="deleteReview()">Delete</button>
              </div>` : ''}
            </div>`;
        });
        html += `</div>`;
      } else {
        html += `<div class="no-reviews-msg">No patron reviews yet. Be the first to share your thoughts.</div>`;
      }

      // Review form
      if (logged_in) {
        const isUpdate   = !!user_review;
        const existStars = user_review ? parseInt(user_review.star_num) : 0;
        const existText  = user_review ? user_review.content : '';

        let starBtns = '';
        for (let i = 1; i <= 5; i++) {
          starBtns += `<button type="button" class="star-btn${isUpdate && i <= existStars ? ' active' : ''}" data-value="${i}">★</button>`;
        }

        html += `
          <div class="review-form-wrapper">
            <h3>${isUpdate ? 'Update Your Review' : 'Write a Review'}</h3>
            <div style="margin-bottom:6px;font-size:0.85rem;color:var(--mocha,#8b7355);">Select your rating</div>
            <div class="star-selector" id="starSelector">${starBtns}</div>
            <input type="hidden" id="starInput" value="${existStars}">
            <textarea id="reviewContent" class="review-textarea" placeholder="Share your experience...">${existText}</textarea>
            <button id="submitBtn" class="btn-coral" ${!isUpdate ? 'disabled' : ''} onclick="submitReview()">
              ${isUpdate ? 'UPDATE REVIEW' : 'SUBMIT REVIEW'}
            </button>
            <span id="reviewMsg" style="margin-left:12px;font-size:0.85rem;color:var(--sunset-coral,#e8735a);"></span>
          </div>`;
      } else {
        html += `
          <div class="review-form-wrapper">
            <h3>Write a Review</h3>
            <p class="login-prompt">Please <a href="index-login.php">sign in</a> to leave a review.</p>
          </div>`;
      }

      document.getElementById('patron-content').innerHTML = html;
      document.getElementById('patron-content').style.display = '';
      document.getElementById('patron-loading').style.display = 'none';

      initStarSelector();
    }

    // Load reviews from api_review.php
    function loadReviews() {
      fetch(`api_review.php?movie_id=${MOVIE_ID}`)
        .then(r => r.json())
        .then(data => {
          if (data.success) renderPatronReviews(data);
        })
        .catch(() => {
          document.getElementById('patron-loading').innerHTML =
            `<p style="color:var(--mocha);font-style:italic;padding:20px 0;">Unable to load reviews. Please refresh the page.</p>`;
        });
    }

    // Submit (create or update) review
    function submitReview() {
      const starInput = document.getElementById('starInput');
      const content   = document.getElementById('reviewContent');
      const btn       = document.getElementById('submitBtn');
      const msg       = document.getElementById('reviewMsg');
      const stars     = parseInt(starInput ? starInput.value : '0');

      if (!stars) return;

      btn.disabled = true;
      btn.textContent = 'Saving…';
      if (msg) msg.textContent = '';

      fetch('api_review_create.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          movie_id: MOVIE_ID,
          star_num: stars,
          content:  content ? content.value.trim() : ''
        })
      })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            loadReviews();
          } else {
            btn.disabled = false;
            btn.textContent = 'SUBMIT REVIEW';
            if (msg) msg.textContent = data.error || 'Something went wrong.';
          }
        })
        .catch(() => {
          btn.disabled = false;
          btn.textContent = 'SUBMIT REVIEW';
          if (msg) msg.textContent = 'Network error. Please try again.';
        });
    }

    // Delete review
    async function deleteReview() {
      const result = await Swal.fire({
        icon: false,
        title: 'Delete Review',
        html:  'Are you sure you want to remove your review? This cannot be undone.',
        showCancelButton:  true,
        confirmButtonText: 'Delete',
        cancelButtonText:  'Cancel',
        reverseButtons: true,
        customClass: {
          popup:         'lumiere-swal-popup',
          title:         'lumiere-swal-title',
          htmlContainer: 'lumiere-swal-html',
          actions:       'lumiere-swal-actions',
          confirmButton: 'lumiere-swal-confirm',
          cancelButton:  'lumiere-swal-cancel',
        },
      });

      if (!result.isConfirmed) return;

      fetch('api_review_delete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ movie_id: MOVIE_ID })
      })
        .then(r => r.json())
        .then(data => {
          if (data.success) loadReviews();
        })
        .catch(() => Swal.fire({
          icon: false,
          title: 'Network Error',
          html:  'Unable to delete your review. Please try again.',
          customClass: {
            popup:         'lumiere-swal-popup',
            title:         'lumiere-swal-title',
            htmlContainer: 'lumiere-swal-html',
            actions:       'lumiere-swal-actions',
            confirmButton: 'lumiere-swal-confirm',
          },
        }));
    }

    // Star selector for review form
    function initStarSelector() {
      const selector  = document.getElementById('starSelector');
      if (!selector) return;

      const starBtns  = selector.querySelectorAll('.star-btn');
      const starInput = document.getElementById('starInput');
      const submitBtn = document.getElementById('submitBtn');
      let selected    = starInput ? parseInt(starInput.value) || 0 : 0;

      function setActive(count) {
        selected = count;
        starBtns.forEach((btn, i) => btn.classList.toggle('active', i < count));
        if (starInput)  starInput.value    = count;
        if (submitBtn)  submitBtn.disabled = (count === 0);
      }

      starBtns.forEach(btn => {
        btn.addEventListener('click', function () { setActive(parseInt(this.dataset.value)); });
        btn.addEventListener('mouseenter', function () {
          const val = parseInt(this.dataset.value);
          starBtns.forEach((b, i) => b.classList.toggle('active', i < val));
        });
      });

      selector.addEventListener('mouseleave', () => {
        starBtns.forEach((b, i) => b.classList.toggle('active', i < selected));
      });
    }

    loadReviews(); // Initial load of patron reviews
  </script>
</body>
</html>