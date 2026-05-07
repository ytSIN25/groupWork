<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LUMIÈRE — Catalog Manager</title>
  <link rel="stylesheet" href="css/base.css?v=5">
  <link rel="stylesheet" href="css/pages/dashboard.css?v=5">
  <link rel="stylesheet" href="css/pages/footer.css?v=5">
  <link rel="stylesheet" href="css/global.css?v=5">
</head>

<body>
  <div class="film-grain"></div>
  <div class="page-transition active" id="pageTransition">
    <span class="trans-logo">LUMIÈRE</span>
  </div>

  <nav class="lumiere-nav liquidGlass-wrapper" style="padding: 15px 5%; border-radius: 0 0 15px 15px; border-bottom: none; background: transparent;">
    <div class="liquidGlass-effect"></div>
    <div class="liquidGlass-tint"></div>
    <div class="liquidGlass-shine"></div>
    <div class="liquidGlass-content" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
      <a href="dashboard-admin.html" class="lumiere-logo" style="gap: 10px;">
        <img src="assets/images/logo.svg?v=5" alt="LUMIÈRE" style="height: 40px;">
        <span style="font-family: var(--font-accent); font-size: 1rem; color: var(--mocha); letter-spacing: 0.2em;">STAFF</span>
      </a>
      <div class="nav-links">
        <a href="dashboard-admin.html" class="nav-link">Dashboard</a>
        <a href="organiser-movies.html" class="nav-link" style="color: var(--sunset-coral);">Catalog</a>
        <a href="organiser-promotions.html" class="nav-link">Promotions</a>
        <a href="dashboard.html" class="nav-link" style="color: var(--sunset-rose);">Exit Staff</a>
      </div>
    </div>
  </nav>

  <div class="page-wrapper org-wrapper" style="padding: 120px 5% 60px;">
    <h1 class="fade-up">Acquire New Celluloid</h1>
    <div class="scale-in liquidGlass-wrapper" style="border-radius: 12px; max-width: 800px; margin: 40px auto 0; padding: 0; border: none; background: transparent;">
      <div class="liquidGlass-effect"></div>
      <div class="liquidGlass-tint" style="background: rgba(255, 255, 255, 0.75);"></div>
      <div class="liquidGlass-shine"></div>
      <div class="liquidGlass-content" style="padding: 50px; position: relative;">
        <div style="position: absolute; top: 20px; left: 20px; right: 20px; bottom: 20px; border: 1px solid var(--mocha); pointer-events: none; opacity: 0.3; border-radius: 4px;"></div>
        <h2 style="text-align: center; font-size: 2rem; font-style: italic; margin-bottom: 30px; color: var(--bg-deep); border-bottom: 2px solid var(--bg-deep); display: inline-block; padding-bottom: 10px; position: relative; left: 50%; transform: translateX(-50%);">
          Acquisition Ledger No. 492
        </h2>
        <form>
          <div style="display: flex; gap: 30px;">
            <div class="form-group" style="flex: 1;">
              <label style="color: var(--bg-deep); font-weight: 600;">Movie ID</label>
              <input type="text" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="e.g. MV-2024-001">
            </div>
            <div class="form-group" style="flex: 1;">
              <label style="color: var(--bg-deep); font-weight: 600;">Organiser ID (User ID)</label>
              <input type="text" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="Enter Staff ID">
            </div>
          </div>

          <div class="form-group">
            <label style="color: var(--bg-deep); font-weight: 600;">Picture Title</label>
            <input type="text" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="Enter Official Title">
          </div>

          <div style="display: flex; gap: 30px;">
            <div class="form-group" style="flex: 1;">
              <label style="color: var(--bg-deep); font-weight: 600;">Release Year</label>
              <input type="number" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="e.g. 2024">
            </div>
            <div class="form-group" style="flex: 1;">
              <label style="color: var(--bg-deep); font-weight: 600;">Director</label>
              <input type="text" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="Name of Director">
            </div>
          </div>

          <div style="display: flex; gap: 30px;">
            <div class="form-group" style="flex: 1;">
              <label style="color: var(--bg-deep); font-weight: 600;">Genre</label>
              <input type="text" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="Drama, Sci-Fi, etc.">
            </div>
            <div class="form-group" style="flex: 1;">
              <label style="color: var(--bg-deep); font-weight: 600;">Duration (min)</label>
              <input type="number" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="e.g. 120">
            </div>
          </div>

          <div class="form-group">
            <label style="color: var(--bg-deep); font-weight: 600;">Starring</label>
            <input type="text" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="Lead Actors">
          </div>

          <div class="form-group">
            <label style="color: var(--bg-deep); font-weight: 600;">Synopsis</label>
            <textarea class="typewriter-input" style="color: var(--bg-deep); height: 100px; resize: none; background: transparent; border-color: var(--mocha);" placeholder="Summary of the picture..."></textarea>
          </div>

          <div style="display: flex; gap: 30px;">
            <div class="form-group" style="flex: 1;">
              <label style="color: var(--bg-deep); font-weight: 600;">Start Date</label>
              <input type="date" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);">
            </div>
            <div class="form-group" style="flex: 1;">
              <label style="color: var(--bg-deep); font-weight: 600;">Base Price (£)</label>
              <input type="number" step="0.01" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="12.00">
            </div>
          </div>

          <div style="display: flex; gap: 30px;">
            <div class="form-group" style="flex: 1;">
              <label style="color: var(--bg-deep); font-weight: 600;">Auditorium No.</label>
              <input type="number" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="1-8">
            </div>
            <div class="form-group" style="flex: 1;">
              <label style="color: var(--bg-deep); font-weight: 600;">Time Slots</label>
              <input type="text" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="14:00, 17:30, 20:00">
            </div>
          </div>

          <div class="form-group">
            <label style="color: var(--bg-deep); font-weight: 600;">Poster Path / URL</label>
            <input type="text" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="assets/images/poster-new.png">
          </div>

          <button class="btn-primary" style="width: 100%; margin-top: 20px; color: var(--bg-deep); border-color: var(--bg-deep);" type="button" onclick="Swal.fire({title:'Approved',text:'Picture added to archive.',icon:'success',background:'#F2E8D5',color:'#0D0B0E',iconColor:'#2A7A7A',confirmButtonColor:'#2A7A7A'}).then(()=>triggerPageTransition('dashboard-admin.html'))">
            Add to Archive
          </button>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/main.js?v=5"></script>
  <script>
    const dz = document.getElementById('dz');
    if (dz) {
      dz.addEventListener('dragover', e => {
        e.preventDefault();
        dz.classList.add('dragover');
      });
      dz.addEventListener('dragleave', e => {
        e.preventDefault();
        dz.classList.remove('dragover');
      });
      dz.addEventListener('drop', e => {
        e.preventDefault();
        dz.classList.remove('dragover');
        dz.innerHTML = '<div style="font-family:monospace; color:var(--bg-deep); font-weight:600;">✓ Negative Processed</div>';
      });
    }
  </script>

  <svg width="0" height="0" style="position: absolute;">
    <filter id="glass-distortion">
      <feTurbulence type="fractalNoise" baseFrequency="0.04" numOctaves="1" result="noise" />
      <feDisplacementMap in="SourceGraphic" in2="noise" scale="4" xChannelSelector="R" yChannelSelector="G" />
    </filter>
  </svg>

</body>

</html>
