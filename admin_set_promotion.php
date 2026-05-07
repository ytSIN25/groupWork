<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LUMIÈRE — Promotions</title>
  <link rel="stylesheet" href="css/base.css?v=5">
  <link rel="stylesheet" href="css/pages/dashboard.css?v=5">
  <link rel="stylesheet" href="css/pages/footer.css?v=5">
  <link rel="stylesheet" href="css/global.css?v=5">
  <style>
    .promo-list {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 25px;
      margin-top: 25px;
    }

    .promo-card {
      border: 2px dashed rgba(212, 168, 83, 0.3);
      padding: 25px;
      background: var(--bg-card);
      text-align: center;
      position: relative;
      border-radius: 6px;
      transition: var(--transition);
    }

    .promo-card:hover {
      border-color: var(--sunset-coral);
      transform: translateY(-5px);
    }

    .promo-card::before,
    .promo-card::after {
      content: '';
      position: absolute;
      top: 50%;
      width: 25px;
      height: 25px;
      background: var(--bg-dark);
      border-radius: 50%;
      transform: translateY(-50%);
    }

    .promo-card::before {
      left: -13px;
    }

    .promo-card::after {
      right: -13px;
    }

    .promo-stamp {
      position: absolute;
      right: 15px;
      top: 15px;
      color: var(--gold);
      font-size: 1rem;
      font-weight: 700;
      transform: rotate(-12deg);
      border: 2px solid var(--gold);
      padding: 3px 10px;
      letter-spacing: 0.1em;
    }
  </style>
</head>

<body>
  <div class="film-grain"></div>
  <div class="page-transition active" id="pageTransition">
    <span class="trans-logo">LUMIÈRE</span>
  </div>

  <nav class="lumiere-nav" style="padding: 15px 5%;">
    <a href="dashboard-admin.html" class="lumiere-logo" style="gap: 10px;">
      <img src="assets/images/logo.svg?v=5" alt="LUMIÈRE" style="height: 40px;">
      <span style="font-family: var(--font-accent); font-size: 1rem; color: var(--mocha); letter-spacing: 0.2em;">STAFF</span>
    </a>
    <div class="nav-links">
      <a href="dashboard-admin.html" class="nav-link">Dashboard</a>
      <a href="organiser-movies.html" class="nav-link">Catalog</a>
      <a href="organiser-promotions.html" class="nav-link" style="color: var(--sunset-coral);">Promotions</a>
      <a href="dashboard.html" class="nav-link" style="color: var(--sunset-rose);">Exit Staff</a>
    </div>
  </nav>

  <div class="page-wrapper org-wrapper" style="padding: 120px 5% 60px; display: flex; gap: 50px;">
    <div style="flex: 1;">
      <h1 class="fade-up" style="margin-bottom: 8px;">Issue Promotion</h1>
      <p style="color: var(--mocha); font-style: italic; font-family: var(--font-accent); margin-bottom: 30px;" class="fade-up">
        Generate coupons for patron redemption.
      </p>
      <div style="background: var(--cream); color: var(--bg-deep); padding: 40px; border-radius: 8px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);" class="scale-in">
        <form id="promoForm">
          <div class="form-group">
            <label style="color: var(--bg-deep); font-weight: 600;">Offer Headline</label>
            <input type="text" class="typewriter-input" style="color: var(--bg-deep);" id="ph" placeholder="e.g. Free Beverage" required>
          </div>
          <div class="form-group">
            <label style="color: var(--bg-deep); font-weight: 600;">Description</label>
            <textarea class="typewriter-input" style="color: var(--bg-deep); height: 60px; resize: none;" id="pd" placeholder="Details..."></textarea>
          </div>
          <div style="display: flex; gap: 20px;">
            <div class="form-group" style="flex: 1;">
              <label style="color: var(--bg-deep); font-weight: 600;">Code</label>
              <input type="text" class="typewriter-input" style="color: var(--bg-deep); text-transform: uppercase;" id="pc" placeholder="DRINK-1926" required>
            </div>
            <div class="form-group" style="flex: 1;">
              <label style="color: var(--bg-deep); font-weight: 600;">Expires</label>
              <input type="date" class="typewriter-input" style="color: var(--bg-deep);" id="pe" required>
            </div>
          </div>
          <button class="btn-primary" style="width: 100%; margin-top: 15px; color: var(--bg-deep); border-color: var(--bg-deep);" type="button" onclick="mintCoupon()">
            Mint Coupon
          </button>
        </form>
      </div>
    </div>
    <div style="flex: 1;">
      <h2 class="fade-up" style="margin-bottom: 15px;">Active Ledgers</h2>
      <div class="promo-list" id="promoList">
        <div class="promo-card fade-up" data-delay="200">
          <div class="promo-stamp">ACTIVE</div>
          <h3 style="color: var(--gold); font-size: 1.4rem;">10% Off Confectionery</h3>
          <p style="color: var(--mocha); font-style: italic; font-size: 0.9rem; margin-top: 8px;">For velvet chocolates.</p>
          <div style="font-family: monospace; border-top: 1px dashed rgba(212, 168, 83, 0.2); margin-top: 12px; padding-top: 10px; letter-spacing: 0.1em;">SNACK-10</div>
        </div>
        <div class="promo-card fade-up" data-delay="300" style="opacity: 0.5;">
          <div class="promo-stamp" style="color: var(--sunset-coral); border-color: var(--sunset-coral);">EXPIRED</div>
          <h3 style="color: var(--mocha); font-size: 1.4rem;">Admit Two</h3>
          <p style="color: var(--mocha); font-style: italic; font-size: 0.9rem; margin-top: 8px;">Matinee screenings only.</p>
          <div style="font-family: monospace; border-top: 1px dashed rgba(212, 168, 83, 0.2); margin-top: 12px; padding-top: 10px; color: var(--sunset-coral);">EXPIRED</div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/main.js?v=5"></script>
  <script>
    function mintCoupon() {
      const f = document.getElementById('promoForm');
      if (!f.checkValidity()) {
        f.reportValidity();
        return;
      }
      const h = document.getElementById('ph').value;
      const c = document.getElementById('pc').value.toUpperCase();
      const d = document.getElementById('pd').value;

      const card = document.createElement('div');
      card.className = 'promo-card';
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      card.innerHTML = `
        <div class="promo-stamp">ACTIVE</div>
        <h3 style="color:var(--gold); font-size:1.4rem;">${h}</h3>
        <p style="color:var(--mocha); font-style:italic; font-size:0.9rem; margin-top:8px;">${d}</p>
        <div style="font-family:monospace; border-top:1px dashed rgba(212,168,83,0.2); margin-top:12px; padding-top:10px; letter-spacing:0.1em;">${c}</div>
      `;
      document.getElementById('promoList').prepend(card);

      setTimeout(() => {
        card.style.transition = 'all 0.5s ease';
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
      }, 10);

      f.reset();
      Swal.fire({
        title: 'Coupon Minted',
        icon: 'success',
        background: '#1A1520',
        color: '#F2E8D5',
        iconColor: '#D4A853',
        confirmButtonColor: '#D4A853',
        timer: 2000,
        showConfirmButton: false
      });
    }
  </script>
</body>

</html>
