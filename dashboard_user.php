<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUMIÈRE - Patron Dashboard</title>
    <link rel="stylesheet" href="css/base.css?v=5">
    <link rel="stylesheet" href="css/pages/dashboard.css?v=5">
    <link rel="stylesheet" href="css/pages/footer.css?v=5">
    <link rel="stylesheet" href="css/global.css?v=5">
    <style>
        /* Interactive Sidebar */
        .sidebar {
            width: 280px;
            flex-shrink: 0;
            position: relative;
            background: rgba(26, 21, 32, 0.5);
            padding: 40px 20px;
            border-radius: 12px;
            border: 1px solid rgba(212, 168, 83, 0.1);
        }

        .tab-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            color: var(--mocha);
            font-family: var(--font-accent);
            font-size: 1.1rem;
            padding: 18px 15px;
            cursor: pointer;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.4s var(--ease-smooth);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .tab-btn:hover {
            background: rgba(212, 168, 83, 0.05);
            color: var(--cream);
            transform: translateX(8px);
        }

        .tab-btn.active {
            background: linear-gradient(90deg, rgba(232, 115, 90, 0.15) 0%, transparent 100%);
            color: var(--sunset-coral);
            border-left: 3px solid var(--sunset-coral);
        }

        /* Movie Ticket Aesthetic */
        .ticket-card {
            background: var(--cream);
            color: var(--bg-deep);
            border-radius: 12px;
            padding: 0;
            margin-bottom: 35px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            position: relative;
            transition: transform 0.4s var(--ease-smooth);
        }

        .ticket-card:hover {
            transform: translateY(-5px) scale(1.01);
        }

        .ticket-main {
            flex: 3;
            padding: 30px;
            border-right: 2px dashed rgba(13, 11, 14, 0.2);
            position: relative;
        }

        .ticket-stub {
            flex: 1;
            padding: 30px;
            background: rgba(212, 168, 83, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .ticket-card::before,
        .ticket-card::after {
            content: '';
            position: absolute;
            right: calc(25% - 15px);
            width: 30px;
            height: 30px;
            background: var(--bg-deep);
            border-radius: 50%;
            z-index: 2;
        }

        .ticket-card::before {
            top: -15px;
        }

        .ticket-card::after {
            bottom: -15px;
        }

        .ticket-title {
            font-family: var(--font-display);
            font-size: 2.2rem;
            font-style: italic;
            margin: 10px 0;
            color: var(--bg-deep);
        }

        .ticket-meta {
            font-family: var(--font-accent);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-size: 0.9rem;
            color: var(--mocha);
        }

        /* Stats in Dashboard */
        .user-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .user-stat-box {
            background: var(--bg-card);
            border: 1px solid rgba(212, 168, 83, 0.1);
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            transition: var(--transition);
        }

        .user-stat-box:hover {
            border-color: var(--sunset-coral);
            transform: translateY(-5px);
        }

        .user-stat-value {
            font-family: var(--font-display);
            font-size: 2.5rem;
            color: var(--gold);
            display: block;
        }

        .user-stat-label {
            font-family: var(--font-accent);
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.1em;
            color: var(--mocha);
        }

        /* Smooth Section Transitions */
        .tab-pane {
            display: none;
            animation: fadeInBlur 0.8s var(--ease-smooth) forwards;
        }

        /* THIS WAS THE MISSING CSS RULE */
        .tab-pane.active {
            display: block;
        }

        @keyframes fadeInBlur {
            from {
                opacity: 0;
                filter: blur(10px);
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                filter: blur(0);
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
  
    
    
    <div class="film-grain"></div>
    <div class="page-transition active" id="pageTransition"><span class="trans-logo">LUMIÈRE</span></div>

    <nav class="lumiere-nav">
        <div class="nav-left" style="display:flex; align-items:center; gap:25px;">
            <a href="index.php" class="lumiere-logo"><img src="assets/images/logo.svg?v=5" alt="LUMIÈRE"
                    style="height:45px;"></a>
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Home</a>
            <a href="movies.php" class="nav-link">Now Showing</a>
            <a href="history.php" class="nav-link">My Tickets</a>
            <a href="dashboard_user.php" class="nav-link" style="color:var(--sunset-coral);">Account</a>
        </div>
    </nav>

    <div class="page-wrapper" style="padding-top:140px; max-width:1400px; margin:0 auto; display:flex; gap:60px;">
        <aside class="sidebar fade-up">
            <div style="text-align:center; margin-bottom:40px;">
                <img src="https://api.dicebear.com/7.x/notionists/svg?seed=Eleanor&backgroundColor=e8735a" alt="Profile"
                    style="width:100px; height:100px; border-radius:50%; border:3px solid var(--gold); margin-bottom:15px; padding:5px; background:var(--bg-card);">
                <h2 style="font-family:var(--font-display); font-style:italic; font-size:1.8rem; color:var(--cream);">
                    Eleanor Vance</h2>
                <p
                    style="font-family:var(--font-accent); color:var(--gold); text-transform:uppercase; font-size:0.8rem; letter-spacing:0.2em;">
                    Silver Screen Member</p>
            </div>

            <button class="tab-btn active" onclick="switchTab(event, 'overview')"><span>Dashboard</span> ✦</button>
            <button class="tab-btn" onclick="switchTab(event, 'tickets')"><span>My Tickets</span> 🎟️</button>
            <button class="tab-btn" onclick="switchTab(event, 'vouchers')"><span>Vouchers</span> 🏷️</button>
            <button class="tab-btn" onclick="switchTab(event, 'history')"><span>Cinematic History</span> 🎞️</button>
            <button class="tab-btn" onclick="switchTab(event, 'preferences')"><span>Preferences</span> 🍸</button>
            <button class="tab-btn" onclick="switchTab(event, 'settings')"><span>Profile Settings</span> ⚙️</button>

            <div class="divider" style="margin:30px 0;"></div>
            <a href="index.php" class="btn-primary"
                style="width:100%; text-align:center; border-color:var(--retro-red); color:var(--retro-red);">Log
                Out</a>
        </aside>

        <main style="flex:1;">
            <div id="overview" class="tab-pane active">
                <div class="section-header fade-up" style="text-align:left;">
                    <h1 style="font-size:3rem; font-style:italic;">Welcome Back, Eleanor</h1>
                    <p>Your portal to the golden age of cinema.</p>
                </div>

                <div class="user-stats fade-up" data-delay="200">
                    <div class="user-stat-box">
                        <span class="user-stat-value">12</span>
                        <span class="user-stat-label">Films This Year</span>
                    </div>
                    <div class="user-stat-box">
                        <span class="user-stat-value">2,840</span>
                        <span class="user-stat-label">Membership Points</span>
                    </div>
                    <div class="user-stat-box">
                        <span class="user-stat-value">Gold</span>
                        <span class="user-stat-label">Tier Progress</span>
                    </div>
                </div>

                <h2 class="fade-up" style="margin-bottom:25px;">Up Next on the Silver Screen</h2>
                <div class="ticket-card fade-up" data-delay="300">
                    <div class="ticket-main">
                        <span class="ticket-meta">LUMIÈRE PREMIUM ADMISSION</span>
                        <h3 class="ticket-title">Blade Runner 2049</h3>
                        <p style="font-family:var(--font-accent); font-size:1.1rem; color:var(--bg-deep); opacity:0.7;">
                            TOMORROW • 20:00 • PROJECTION HUB</p>
                        <div style="margin-top:20px; display:flex; gap:15px;">
                            <span
                                style="background:var(--bg-deep); color:var(--cream); padding:5px 15px; border-radius:4px; font-family:var(--font-accent);">CIRCLE
                                G14</span>
                            <span
                                style="background:var(--bg-deep); color:var(--cream); padding:5px 15px; border-radius:4px; font-family:var(--font-accent);">CIRCLE
                                G15</span>
                        </div>
                    </div>
                    <div class="ticket-stub">
                        <div class="qr-placeholder" style="width:80px; height:80px; margin-bottom:15px;"></div>
                        <span
                            style="font-family:'Courier New', monospace; font-size:0.8rem; font-weight:bold;">#LX-99281</span>
                    </div>
                </div>
            </div>

            <div id="tickets" class="tab-pane">
                <h1 style="font-style:italic; margin-bottom:40px;">Your Active Admissions</h1>
                <div class="ticket-card">
                    <div class="ticket-main">
                        <span class="ticket-meta">Special Screening</span>
                        <h3 class="ticket-title">Nosferatu</h3>
                        <p style="font-family:var(--font-accent); color:var(--bg-deep); opacity:0.7;">OCT 31 • 22:30 •
                            THE VAULT</p>
                    </div>
                    <div class="ticket-stub">
                        <div class="qr-placeholder" style="width:60px; height:60px; margin-bottom:10px;"></div>
                        <button class="btn-primary"
                            style="padding:5px 10px; font-size:0.7rem; color:var(--bg-deep); border-color:var(--bg-deep);">View
                            Pass</button>
                    </div>
                </div>
            </div>

            <div id="vouchers" class="tab-pane">
                <h1 style="font-style:italic; margin-bottom:40px;">Your Vouchers</h1>
                <div class="voucher-grid">
                    <div class="coupon-card">
                        <h3 style="color:var(--gold); font-size:1.6rem; margin-bottom:10px;">Complimentary Beverage</h3>
                        <p style="color:var(--mocha); font-style:italic; margin-bottom:20px;">Redeem at the lobby bar.</p>
                        <div style="font-family:monospace; font-size:1.1rem; border-top:1px dashed rgba(212,168,83,0.3); padding-top:12px; letter-spacing:0.15em;">DRINK-1926-AB</div>
                    </div>
                    <div class="coupon-card">
                        <h3 style="color:var(--gold); font-size:1.6rem; margin-bottom:10px;">Admit Two</h3>
                        <p style="color:var(--mocha); font-style:italic; margin-bottom:20px;">Valid for any matinee this weekend.</p>
                        <div style="font-family:monospace; font-size:1.1rem; border-top:1px dashed rgba(212,168,83,0.3); padding-top:12px; letter-spacing:0.15em;">MAT-GUEST-XY</div>
                    </div>
                    <div class="coupon-card" style="opacity:0.5;">
                        <h3 style="color:var(--mocha); font-size:1.6rem; margin-bottom:10px;">10% Off Snacks</h3>
                        <p style="color:var(--mocha); font-style:italic; margin-bottom:20px;">Valid for velvet chocolates.</p>
                        <div style="font-family:monospace; font-size:1.1rem; border-top:1px dashed rgba(212,168,83,0.3); padding-top:12px; color:var(--sunset-coral); text-decoration:line-through;">EXPIRED</div>
                    </div>
                </div>
            </div>

            <div id="history" class="tab-pane">
                <h1 style="font-style:italic; margin-bottom:40px;">Cinematic History</h1>
                <div class="reviews-grid">
                    <div class="review-card">
                        <div class="ink-stars">★★★★★</div>
                        <h3>Oppenheimer (70mm)</h3>
                        <p class="review-text">"The experience at Lumière is unmatched. The restoration was pristine."
                        </p>
                        <span class="review-author">- Visited Aug 12, 2023</span>
                    </div>
                </div>
            </div>

            <div id="preferences" class="tab-pane">
                <h1 style="font-style:italic; margin-bottom:40px;">Patron Preferences</h1>
                <div class="vintage-card">
                    <div class="corner-dec corner-tl"></div>
                    <div class="corner-dec corner-tr"></div>
                    <div class="form-group">
                        <label>Preferred Seating Area</label>
                        <select>
                            <option>The Stalls (Front)</option>
                            <option selected>The Circle (Balcony)</option>
                            <option>Royal Box</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Lobby Bar Favorite</label>
                        <input type="text" value="Sunset Boulevard Sour" class="typewriter-input">
                    </div>
                    <button class="btn-coral" style="margin-top:20px;">Save Aesthetics</button>
                </div>
            </div>

            <div id="settings" class="tab-pane">
                <h1 style="font-style:italic; margin-bottom:40px;">Profile Settings</h1>
                <div class="vintage-card">
                    <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                        <div class="form-group"><label>First Name</label><input type="text" value="Eleanor"></div>
                        <div class="form-group"><label>Last Name</label><input type="text" value="Vance"></div>
                        <div class="form-group" style="grid-column: span 2;"><label>Email</label><input type="email"
                                value="eleanor.v@lumiere.com"></div>
                    </div>
                    <button class="btn-coral" style="margin-top:20px;">Update Profile</button>
                </div>
            </div>
        </main>
    </div>

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

    <script src="js/main.js?v=5"></script>
    <script>

        const currentPatron = API.getCurrentUser();
        if(!currentPatron) {
            window.location.href = 'index_login.php';
        }

        function initUser() {

            const sidebar = document.querySelector('.sidebar');
            sidebar.querySelector('img').src = currentPatron.avatar;
            sidebar.querySelector('h2').innerText = currentPatron.name;
            sidebar.querySelector('p').innerText = currentPatron.tier;

            document.querySelector('.section-header h1').innerText = `Welcome Back, ${currentPatron.name.split(' ')[0]}`;

            const stats = document.querySelectorAll('.user-stat-value');
            stats[0].innerText = currentPatron.filmsThisYear;
            stats[1].innerText = currentPatron.points;
        }

        function switchTab(event, tabId) {
            document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));

            document.getElementById(tabId).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        initUser();
    </script>
</body>

</html>