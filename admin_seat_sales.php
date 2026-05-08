<?php
require_once 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index_login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LUMIÈRE | Live Box Office</title>
  <link rel="stylesheet" href="css/base.css?v=5">
  <link rel="stylesheet" href="css/pages/dashboard.css?v=5">
  <link rel="stylesheet" href="css/pages/footer.css?v=5">
  <link rel="stylesheet" href="css/global.css?v=5">
  <style>
    .ticker {
      background: var(--bg-deep);
      border: 2px solid var(--gold);
      padding: 20px;
      font-family: 'Courier New', monospace;
      font-size: 2rem;
      color: var(--gold);
      text-align: center;
      box-shadow: 0 0 15px rgba(212, 168, 83, 0.3);
    }

    .ticker span {
      animation: blink 1s infinite alternate;
    }

    @keyframes blink {
      from {
        opacity: 1;
      }

      to {
        opacity: 0.6;
      }
    }

    .seat-map {
      display: flex;
      flex-direction: column;
      gap: 12px;
      align-items: center;
      margin: 30px 0;
    }

    .seat-row {
      display: flex;
      gap: 10px;
      align-items: center;
    }

    .seat {
      width: 22px;
      height: 22px;
      border-radius: 4px;
      transition: all 0.3s var(--ease-smooth);
      opacity: 0;
      transform: scale(0);
      animation: seat-intro 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    .seat.available {
      background: rgba(212, 168, 83, 0.2);
      border: 1px solid rgba(212, 168, 83, 0.3);
    }

    .seat.taken {
      background: var(--sunset-coral);
      box-shadow: 0 0 10px rgba(232, 115, 90, 0.3);
      border: 1px solid var(--sunset-coral);
    }

    .row-label {
      font-family: var(--font-accent);
      color: var(--mocha);
      font-size: 0.8rem;
      width: 20px;
      text-align: center;
      opacity: 0.6;
    }

    @keyframes seat-intro {
      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .screen-line {
      width: 80%;
      height: 4px;
      background: linear-gradient(90deg, transparent, var(--gold), transparent);
      border-radius: 10px;
      margin: 0 auto 40px;
      box-shadow: 0 4px 15px rgba(212, 168, 83, 0.2);
      position: relative;
    }

    .screen-line::after {
      content: 'SCREEN';
      position: absolute;
      top: -20px;
      left: 50%;
      transform: translateX(-50%);
      font-family: var(--font-accent);
      font-size: 0.7rem;
      letter-spacing: 0.4em;
      color: var(--gold);
      opacity: 0.6;
    }

    .tx-slide-in {
      animation: slide-in 0.5s var(--ease-bounce) forwards;
      opacity: 0;
      transform: translateX(30px);
    }

    @keyframes slide-in {
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }
  </style>
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
      <a href="admin_add_movie.php" class="nav-link">Catalog</a>
      <a href="admin_seat_sales.php" class="nav-link" style="color: var(--sunset-coral);">Live Sales</a>
      <a href="admin_set_promotion.php" class="nav-link">Promotions</a>
      <a href="index.php" class="nav-link" style="color: var(--sunset-rose);">Exit Staff</a>
    </div>
  </nav>

  <div class="page-wrapper org-wrapper" style="padding: 120px 5% 50px;">
    <h1 class="fade-up" style="margin-bottom: 8px;">Oppenheimer — Live Box Office</h1>
    <p style="color: var(--mocha); font-style: italic; font-family: var(--font-accent); margin-bottom: 30px;" class="fade-up" data-delay="100">
      Real-time seat availability and transactions
    </p>

    <div class="fade-up" style="max-width: 900px; margin: 30px auto 0;">
      <div class="theatre-blueprint" style="padding: 60px 40px; border: 1px solid rgba(212, 168, 83, 0.1); border-radius: 12px; background: rgba(13, 11, 14, 0.4); position: relative;">
        <div class="screen-line"></div>
        <div class="seat-map" id="seatMap"></div>
        <div class="seat-legend" style="margin-top: 40px; border-top: 1px dashed rgba(212, 168, 83, 0.1); padding-top: 25px; display: flex; gap: 30px; justify-content: center; align-items: center;">
          <div style="display: flex; align-items: center; gap: 10px; font-family: var(--font-accent); color: var(--cream); opacity: 0.8; font-size: 0.9rem;">
            <div class="seat available" style="opacity: 1; transform: none; animation: none;"></div> Available
          </div>
          <div style="display: flex; align-items: center; gap: 10px; font-family: var(--font-accent); color: var(--cream); opacity: 0.8; font-size: 0.9rem;">
            <div class="seat taken" style="opacity: 1; transform: none; animation: none;"></div> Reserved
          </div>
          <div style="margin-left: 40px; font-family: 'Courier New', monospace; color: var(--gold); border-left: 1px solid rgba(212, 168, 83, 0.2); padding-left: 40px;">
            Occupancy: <span id="soldCount">0</span>/72
          </div>
        </div>
      </div>
    </div>

    <div class="fade-up" style="margin-top: 60px;">
      <h2 style="margin-bottom: 20px; font-style: italic;">Daily Transaction Ledger</h2>
      <div style="background: var(--bg-card); border: 1px solid rgba(212, 168, 83, 0.15); border-radius: 8px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; font-family: var(--font-accent);">
          <thead>
            <tr style="background: rgba(212, 168, 83, 0.05); color: var(--gold); text-align: left;">
              <th style="padding: 15px 20px; font-size: 0.8rem; letter-spacing: 0.1em; text-transform: uppercase;">Order Ref</th>
              <th style="padding: 15px 20px; font-size: 0.8rem; letter-spacing: 0.1em; text-transform: uppercase;">Time</th>
              <th style="padding: 15px 20px; font-size: 0.8rem; letter-spacing: 0.1em; text-transform: uppercase;">Movie ID</th>
              <th style="padding: 15px 20px; font-size: 0.8rem; letter-spacing: 0.1em; text-transform: uppercase;">Seats Allocated</th>
              <th style="padding: 15px 20px; font-size: 0.8rem; letter-spacing: 0.1em; text-transform: uppercase;">Total Amt</th>
              <th style="padding: 15px 20px; font-size: 0.8rem; letter-spacing: 0.1em; text-transform: uppercase; text-align: right;">Status</th>
            </tr>
          </thead>
          <tbody id="ledgerBody">
            <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
              <td style="padding: 15px 20px; color: var(--cream);">TX-99201</td>
              <td style="padding: 15px 20px; opacity: 0.7;">14:20:05</td>
              <td style="padding: 15px 20px; color: var(--gold);">MV-2023-082</td>
              <td style="padding: 15px 20px;">A1, A2</td>
              <td style="padding: 15px 20px; color: var(--gold);">£36.00</td>
              <td style="padding: 15px 20px; text-align: right;">
                <span style="color: var(--retro-mint); font-size: 0.8rem; border: 1px solid var(--retro-mint); padding: 2px 8px; border-radius: 20px;">CLEARED</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="js/main.js?v=5"></script>
  <script>
    const map = document.getElementById('seatMap');
    const rows = ['A', 'B', 'C', 'D', 'E', 'F'];
    const cols = 12;
    let sold = 0;
    let emptySeats = [];

    rows.forEach((row, rIdx) => {
      const rd = document.createElement('div');
      rd.className = 'seat-row';

      const l = document.createElement('div');
      l.className = 'row-label';
      l.textContent = row;
      rd.appendChild(l);

      for (let i = 1; i <= cols; i++) {
        const s = document.createElement('div');
        const taken = Math.random() > 0.6;
        s.id = `seat-${row}${i}`;
        s.style.animationDelay = ((rIdx * cols + i) * 0.015) + 's';

        if (taken) {
          s.className = 'seat taken';
          sold++;
        } else {
          s.className = 'seat available';
          emptySeats.push(`${row}${i}`);
        }
        rd.appendChild(s);
      }

      const le = document.createElement('div');
      le.className = 'row-label';
      le.textContent = row;
      rd.appendChild(le);

      map.appendChild(rd);
    });

    document.getElementById('soldCount').textContent = sold;
    const ledgerBody = document.getElementById('ledgerBody');

    function generateTxId() {
      return 'TX-' + Math.random().toString(36).substr(2, 5).toUpperCase();
    }

    function addTx(seats, amt) {
      const t = new Date().toLocaleTimeString('en-GB', {
        hour12: false,
        hour: "numeric",
        minute: "numeric",
        second: "numeric"
      });
      const id = generateTxId();

      const lTr = document.createElement('tr');
      lTr.style.borderBottom = '1px solid rgba(255,255,255,0.05)';
      lTr.className = 'tx-slide-in';
      lTr.innerHTML = `
        <td style="padding:15px 20px; color:var(--cream);">${id}</td>
        <td style="padding:15px 20px; opacity:0.7;">${t}</td>
        <td style="padding:15px 20px; color:var(--gold);">MV-2023-082</td>
        <td style="padding:15px 20px;">${seats}</td>
        <td style="padding:15px 20px; color:var(--gold);">£${amt}</td>
        <td style="padding:15px 20px; text-align:right;">
          <span style="color:var(--retro-mint); font-size:0.8rem; border:1px solid var(--retro-mint); padding:2px 8px; border-radius:20px;">CLEARED</span>
        </td>
      `;
      ledgerBody.prepend(lTr);
      if (ledgerBody.children.length > 15) ledgerBody.lastElementChild.remove();
    }

    addTx("A1,A2", "36.00");
    addTx("D5", "12.00");

    setInterval(() => {
      if (emptySeats.length > 0) {
        const n = Math.floor(Math.random() * 2) + 1;
        let s = [];
        for (let i = 0; i < n; i++) {
          if (!emptySeats.length) break;
          const idx = Math.floor(Math.random() * emptySeats.length);
          const id = emptySeats.splice(idx, 1)[0];
          s.push(id);
          const el = document.getElementById(`seat-${id}`);
          if (el) {
            el.classList.remove('available');
            el.classList.add('taken');
            el.style.boxShadow = '0 0 15px var(--sunset-coral)';
            setTimeout(() => el.style.boxShadow = 'none', 1000);
            sold++;
          }
        }
        if (s.length) {
          document.getElementById('soldCount').textContent = sold;
          addTx(s.join(','), (s.length * 12).toFixed(2));
        }
      }
    }, 4000);
  </script>
</body>

</html>
