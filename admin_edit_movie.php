<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LUMIÈRE — Edit Movie</title>
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

  <nav class="lumiere-nav" style="padding: 15px 5%;">
    <a href="dashboard_admin.php" class="lumiere-logo" style="gap: 10px;">
      <img src="assets/images/logo.svg?v=5" alt="LUMIÈRE" style="height: 40px;">
      <span style="font-family: var(--font-accent); font-size: 1rem; color: var(--mocha); letter-spacing: 0.2em;">STAFF</span>
    </a>
    <div class="nav-links">
      <a href="dashboard_admin.php" class="nav-link">Dashboard</a>
      <a href="admin_add_movie.php" class="nav-link" style="color: var(--sunset-coral);">Catalog</a>
      <a href="admin_set_promotion.php" class="nav-link">Promotions</a>
      <a href="index.php" class="nav-link" style="color: var(--sunset-rose);">Exit Staff</a>
    </div>
  </nav>

  <div class="page-wrapper org-wrapper" style="padding: 120px 5% 60px;">
    <h1 class="fade-up" style="margin-bottom: 30px;">Edit: Oppenheimer</h1>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; max-width: 1100px;">
      <div class="fade-up">
        <div style="background: var(--bg-card); padding: 30px; border: 1px solid rgba(212, 168, 83, 0.1); border-radius: 8px;">
          <h3 style="color: var(--cream); margin-bottom: 20px; font-size: 1.4rem;">Picture Details</h3>
          <div style="display: flex; gap: 20px;">
            <div class="form-group" style="flex: 1;">
              <label>Movie ID</label>
              <input class="typewriter-input" value="MV-2023-082" readonly>
            </div>
            <div class="form-group" style="flex: 1;">
              <label>Organiser ID</label>
              <input class="typewriter-input" value="USR-STAFF-01">
            </div>
          </div>
          <div class="form-group">
            <label>Title</label>
            <input class="typewriter-input" value="Oppenheimer">
          </div>
          <div style="display: flex; gap: 20px;">
            <div class="form-group" style="flex: 1;">
              <label>Director</label>
              <input class="typewriter-input" value="Christopher Nolan">
            </div>
            <div class="form-group" style="flex: 1;">
              <label>Year</label>
              <input class="typewriter-input" value="2023" type="number">
            </div>
          </div>
          <div style="display: flex; gap: 20px;">
            <div class="form-group" style="flex: 1;">
              <label>Genre</label>
              <input class="typewriter-input" value="Drama, Thriller">
            </div>
            <div class="form-group" style="flex: 1;">
              <label>Runtime (min)</label>
              <input class="typewriter-input" value="180" type="number">
            </div>
          </div>
          <div class="form-group">
            <label>Starring</label>
            <input class="typewriter-input" value="Cillian Murphy, Emily Blunt, Robert Downey Jr.">
          </div>
          <div class="form-group">
            <label>Synopsis</label>
            <textarea class="typewriter-input" style="height: 120px; resize: none;">During World War II, Lt. Gen. Leslie Groves Jr. appoints physicist J. Robert Oppenheimer to work on the top-secret Manhattan Project...</textarea>
          </div>
          <div style="display: flex; gap: 20px;">
            <div class="form-group" style="flex: 1;">
              <label>Start Date</label>
              <input class="typewriter-input" type="date" value="2023-07-21">
            </div>
            <div class="form-group" style="flex: 1;">
              <label>Auditorium No.</label>
              <input class="typewriter-input" type="number" value="1">
            </div>
          </div>
          <div class="form-group">
            <label>Time Slots</label>
            <input class="typewriter-input" value="13:00, 16:30, 20:00">
          </div>
          <div class="form-group">
            <label>Poster Path</label>
            <input class="typewriter-input" value="assets/images/poster-oppenheimer.png">
          </div>
          <button class="btn-primary" style="width: 100%;" onclick="Swal.fire({title:'Saved',text:'Amendments recorded.',icon:'success',background:'#1A1520',color:'#F2E8D5',iconColor:'#D4A853',confirmButtonColor:'#D4A853'})">
            Save Changes
          </button>
        </div>
      </div>

      <div class="fade-right">
        <div style="background: var(--bg-card); padding: 30px; border: 1px solid rgba(212, 168, 83, 0.1); border-radius: 8px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="color: var(--cream); font-size: 1.4rem;">Ticket Tiers</h3>
            <button class="btn-primary" style="padding: 6px 15px; font-size: 0.85rem;" id="addTierBtn">+ Add Tier</button>
          </div>
          <p style="color: var(--mocha); font-style: italic; margin-bottom: 20px; font-size: 0.95rem;">Drag to reorder tiers</p>
          <ul id="tierList" style="list-style: none; display: flex; flex-direction: column; gap: 12px;">
            <li style="background: rgba(212, 168, 83, 0.08); padding: 15px 20px; border: 1px solid rgba(212, 168, 83, 0.15); border-radius: 6px; display: flex; justify-content: space-between; align-items: center; cursor: grab;">
              <div>
                <strong style="color: var(--gold);">Stalls</strong>
                <span style="color: var(--mocha); margin-left: 15px; font-style: italic;">£12.00</span>
              </div>
              <button style="background: none; border: none; color: var(--sunset-coral); cursor: pointer; font-size: 1.2rem;" onclick="this.parentElement.remove()">✕</button>
            </li>
            <li style="background: rgba(212, 168, 83, 0.08); padding: 15px 20px; border: 1px solid rgba(212, 168, 83, 0.15); border-radius: 6px; display: flex; justify-content: space-between; align-items: center; cursor: grab;">
              <div>
                <strong style="color: var(--gold);">Circle</strong>
                <span style="color: var(--mocha); margin-left: 15px; font-style: italic;">£18.00</span>
              </div>
              <button style="background: none; border: none; color: var(--sunset-coral); cursor: pointer; font-size: 1.2rem;" onclick="this.parentElement.remove()">✕</button>
            </li>
            <li style="background: rgba(212, 168, 83, 0.08); padding: 15px 20px; border: 1px solid rgba(212, 168, 83, 0.15); border-radius: 6px; display: flex; justify-content: space-between; align-items: center; cursor: grab;">
              <div>
                <strong style="color: var(--gold);">Royal Box</strong>
                <span style="color: var(--mocha); margin-left: 15px; font-style: italic;">£45.00</span>
              </div>
              <button style="background: none; border: none; color: var(--sunset-coral); cursor: pointer; font-size: 1.2rem;" onclick="this.parentElement.remove()">✕</button>
            </li>
          </ul>
        </div>
        <div style="margin-top: 30px;">
          <img src="assets/images/poster-oppenheimer.png" alt="Current Poster" style="width: 100%; border-radius: 8px; border: 1px solid rgba(212, 168, 83, 0.1);">
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
  <script src="js/main.js?v=5"></script>
  <script>
    new Sortable(document.getElementById('tierList'), {
      animation: 250,
      ghostClass: 'sortable-ghost'
    });
    document.getElementById('addTierBtn').addEventListener('click', () => {
      const li = document.createElement('li');
      li.style.cssText = 'background:rgba(212,168,83,0.08); padding:15px 20px; border:1px solid rgba(212,168,83,0.15); border-radius:6px; display:flex; justify-content:space-between; align-items:center; cursor:grab; opacity:0; transition:opacity 0.3s;';
      li.innerHTML = '<div><input class="typewriter-input" placeholder="Tier Name" style="width:100px;"> <input class="typewriter-input" placeholder="£0.00" style="width:70px;"></div><button style="background:none; border:none; color:var(--sunset-coral); cursor:pointer; font-size:1.2rem;" onclick="this.parentElement.remove()">✕</button>';
      document.getElementById('tierList').appendChild(li);
      requestAnimationFrame(() => li.style.opacity = '1');
    });
  </script>
</body>

</html>
