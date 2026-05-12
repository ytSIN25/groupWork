<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index_login.php');
    exit();
}

// Page data setup
$mid = $_GET['movie_id'] ?? 1;
$stmt = $conn->prepare("SELECT * FROM movies WHERE movie_id = ?");
$stmt->bind_param("i", $mid);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();

if (!$movie) die("Error: Movie not found.");

$tier = $_GET['tier'] ?? 'Stalls';
$price_multiplier = match($tier) {
    'Circle'    => 1.5,
    'Royal Box' => 3.0,
    default     => 1.0,
};

// Current user
$u_stmt = $conn->prepare("SELECT name, email FROM users WHERE user_id = ?");
$u_stmt->bind_param("i", $_SESSION['user_id']);
$u_stmt->execute();
$current_user = $u_stmt->get_result()->fetch_assoc();

// Showtime slots
$st_stmt = $conn->prepare("SELECT showtime_id, start_time FROM showtimes WHERE movie_id = ? ORDER BY start_time");
$st_stmt->bind_param("i", $mid);
$st_stmt->execute();
$time_slots = $st_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Taken seats per slot
$orders_stmt = $conn->prepare("SELECT show_date, show_time, seats FROM orders WHERE movie_id = ?");
$orders_stmt->bind_param("i", $mid);
$orders_stmt->execute();
$orders_res = $orders_stmt->get_result();

$taken_seats_data = [];
while ($row = $orders_res->fetch_assoc()) {
    $key = $row['show_date'] . '_' . substr($row['show_time'], 0, 8);
    $taken_seats_data[$key] = array_merge(
        $taken_seats_data[$key] ?? [],
        array_map('trim', explode(',', $row['seats']))
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUMIÈRE - Reservation</title>
    <link rel="stylesheet" href="css/base.css?v=5">
    <link rel="stylesheet" href="css/pages/booking.css?v=5">
    <link rel="stylesheet" href="css/pages/footer.css?v=5">
    <link rel="stylesheet" href="css/global.css?v=5">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
</head>

<body>
    <div class="film-grain"></div>
    <div class="page-transition active" id="pageTransition">
        <span class="trans-logo">LUMIÈRE</span>
    </div>

    <nav class="lumiere-nav">
        <a href="movies.php" class="lumiere-logo">
            <img src="assets/images/logo.svg?v=5" alt="LUMIÈRE">
        </a>
        <div class="nav-links">
            <a href="movies.php" class="nav-link">Cancel Reservation</a>
        </div>
    </nav>

    <div class="page-wrapper seat-booking-wrapper">

        <!-- ---------- Left: Theatre Blueprint ---------- -->
        <div class="theatre-blueprint fade-up">
            <div class="booking-selectors" style="margin-bottom:40px; display:flex; flex-direction:column; gap:20px; align-items:center;">

                <!-- ----- Date Selector ----- -->
                <div class="date-selector">
                    <p style="font-family:var(--font-accent); color:var(--mocha); font-size:0.75rem; text-transform:uppercase; letter-spacing:0.2em; margin-bottom:10px; opacity:0.6;">1. Choose Date</p>
                    <div class="date-scroll-container">
                        <?php
                        $start = new DateTime($movie['start_date']);
                        $today = new DateTime('today');
                        for ($i = 0; $i < 14; $i++):
                            $d = clone $start;
                            $d->modify("+$i days");
                            if ($d < $today) continue;
                            $ds = $d->format('Y-m-d');
                        ?>
                            <button class="date-btn"
                                    onclick="selectDate('<?= $ds ?>', this)"
                                    style="flex:0 0 auto; padding:10px 15px; border-radius:6px; border:1px solid rgba(212,168,83,0.2); background:rgba(212,168,83,0.05); color:var(--cream); cursor:pointer; transition:all 0.3s;">
                                <div style="font-size:0.7rem; text-transform:uppercase;"><?= $d->format('D') ?></div>
                                <div style="font-size:1.1rem; font-weight:700;"><?= $d->format('d') ?></div>
                                <div style="font-size:0.7rem;"><?= $d->format('M') ?></div>
                            </button>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- ----- Time Selector ----- -->
                <div class="time-selector">
                    <p style="font-family:var(--font-accent); color:var(--mocha); font-size:0.75rem; text-transform:uppercase; letter-spacing:0.2em; margin-bottom:10px; opacity:0.6;">2. Choose Time</p>
                    <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;" id="timeList">
                        <?php foreach ($time_slots as $slot): ?>
                            <button class="showtime-btn"
                                    onclick="selectTime('<?= $slot['start_time'] ?>', this)"
                                    style="background:rgba(212,168,83,0.05); border:1px solid rgba(212,168,83,0.2); color:var(--cream); padding:10px 25px; border-radius:4px; font-family:var(--font-accent); cursor:pointer; transition:all 0.3s; min-width:100px; font-weight:600;">
                                <?= substr($slot['start_time'], 0, 5) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <input type="hidden" id="selectedDate" value="">
                <input type="hidden" id="selectedShowTime" value="">
            </div>

            <div class="screen-curve">THE SILVER SCREEN</div>
            <div class="seat-map" id="seatMap"></div>

            <div class="seat-legend">
                <div class="legend-item"><div class="seat available" style="cursor:default;"></div> Available</div>
                <div class="legend-item"><div class="seat taken"     style="cursor:default;"></div> Reserved</div>
                <div class="legend-item"><div class="seat selected"  style="cursor:default;"></div> Your Selection</div>
            </div>
        </div>

        <!-- ---------- Right: Booking Panel ---------- -->
        <div class="booking-panel fade-right">

            <!-- ---- Step Dots ----- -->
            <div class="step-indicator">
                <div class="step-dot active" id="dot1">1</div>
                <div class="step-connector" id="line1"></div>
                <div class="step-dot" id="dot2">2</div>
                <div class="step-connector" id="line2"></div>
                <div class="step-dot" id="dot3">3</div>
                <div class="step-connector" id="line3"></div>
                <div class="step-dot" id="dot4">4</div>
            </div>

            <!-- ----- Step 1: Seat Summary ----- -->
            <div id="step1" class="step-content active">
                <h2 style="margin-bottom:20px;">Reservation</h2>
                <h3 style="margin-bottom:20px;"><?= htmlspecialchars($movie['movie_name']) ?></h3>

                <ul class="booking-summary-list" id="selectionList">
                    <li><span style="color:var(--mocha); font-style:italic;">No seats selected</span></li>
                </ul>

                <ul class="booking-summary-list" style="border-top:1px solid rgba(212,168,83,0.1);">
                    <li class="discount-line" id="discountLine">
                        <span>Promo Discount</span>
                        <span id="discountAmount" style="color:#6ee79a;">- € 0.00</span>
                    </li>
                    <li class="booking-total">Total <span id="totalPrice">€ 0.00</span></li>
                </ul>

                <button class="btn-primary"
                        style="width:100%; margin-top:10px; opacity:0.5; pointer-events:none;"
                        id="step1Btn"
                        onclick="nextStep(2)">Continue</button>
            </div>

            <!-- ----- Step 2: Patron Details ----- -->
            <div id="step2" class="step-content">
                <h2 style="margin-bottom:20px;">Patron Details</h2>
                <form onsubmit="event.preventDefault(); nextStep(3);">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" value="<?= htmlspecialchars($current_user['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="<?= htmlspecialchars($current_user['email']) ?>" required>
                    </div>
                    <button class="btn-primary" style="width:100%; margin-top:10px;" type="button" onclick="nextStep(1)">Back</button>
                    <button class="btn-coral"   style="width:100%; margin-top:10px;" type="submit">Continue</button>
                </form>
            </div>

            <!-- ----- Step 3: Promotion Code ----- -->
            <div id="step3" class="step-content">
                <h2 style="margin-bottom:20px;">Apply Promotion</h2>
                <ul class="booking-summary-list" style="margin-bottom:22px;">
                    <li><span style="color:var(--mocha); font-style:italic;">Have a promo code? Enter it below to receive a discount.</span></li>
                </ul>

                <div class="form-group" style="margin-bottom:8px;">
                    <label>Promo Code</label>
                    <div class="promo-input-row">
                        <input type="text"
                               id="promoCodeInput"
                               placeholder="e.g. LUMIERE20"
                               autocomplete="off"
                               oninput="onPromoInput()"
                               style="font-family:var(--font-accent);">
                        <button class="btn-apply" id="applyPromoBtn" onclick="applyPromo()" disabled>Apply</button>
                    </div>
                </div>

                <div class="promo-feedback" id="promoFeedback">
                    <div class="promo-icon" id="promoIcon"></div>
                    <div class="promo-feedback-text">
                        <strong id="promoFeedbackTitle"></strong>
                        <span id="promoFeedbackBody"></span>
                    </div>
                </div>

                <div id="promoBadgeRow" style="display:none;">
                    <div class="promo-discount-badge">
                        🎟 <span id="promoBadgeText"></span>
                        <button class="promo-remove-btn" onclick="removePromo()" title="Remove promo">✕</button>
                    </div>
                </div>

                <button class="btn-primary" style="width:100%; margin-top:24px;" type="button" onclick="nextStep(2)">Back</button>
                <button class="btn-coral"   style="width:100%; margin-top:10px;" type="button" id="step3ContinueBtn" onclick="nextStep(4)">
                    Continue to Payment
                </button>

                <p class="promo-skip-note">
                    No code? <span class="promo-skip-link" onclick="nextStep(4)">Skip this step</span>
                </p>
            </div>

            <!-- ----- Step 4: Payment ----- -->
            <div id="step4" class="step-content">
                <h2 style="margin-bottom:20px;">Settle Account</h2>

                <div style="background:rgba(212,168,83,0.06); border:1px solid rgba(212,168,83,0.15); border-radius:6px; padding:12px 16px; margin-bottom:20px; font-size:0.9rem;">
                    <div style="display:flex; justify-content:space-between; color:var(--cream); margin-bottom:4px;">
                        <span>Subtotal</span>
                        <span id="paySubtotal">€ 0.00</span>
                    </div>
                    <div id="payDiscountRow" style="display:none; justify-content:space-between; color:#6ee79a; margin-bottom:4px;">
                        <span>Promo Discount</span>
                        <span id="payDiscountAmt">- € 0.00</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-weight:700; color:var(--gold); border-top:1px solid rgba(212,168,83,0.15); padding-top:8px; margin-top:4px;">
                        <span>Total Due</span>
                        <span id="payTotal">€ 0.00</span>
                    </div>
                </div>

                <form id="paymentForm">
                    <div class="form-group">
                        <label>Card Number</label>
                        <input type="text" id="cc_number" placeholder="XXXX XXXX XXXX XXXX" required>
                    </div>
                    <div style="display:flex; gap:15px;">
                        <div class="form-group" style="flex:1;">
                            <label>Expiry</label>
                            <input type="text" id="cc_expiry" placeholder="MM/YY" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>CVC</label>
                            <input type="text" id="cc_cvc" placeholder="123" required>
                        </div>
                    </div>
                    <button class="btn-primary" style="width:100%;"                  type="button" onclick="nextStep(3)">Back</button>
                    <button class="btn-coral"   style="width:100%; margin-top:10px;" type="submit" id="payBtn">Complete Reservation</button>
                </form>
            </div>

        </div>
    </div>

    <!-- ---------- Ticket Modal ---------- -->
    <div class="ticket-reveal-modal" id="ticketModal">
        <div class="e-ticket">
            <div class="e-ticket-main">
                <div class="watermark">LUMIÈRE</div>
                <div style="display:flex; justify-content:space-between; align-items:flex-end;">
                    <h2 style="font-size:2.2rem; font-style:italic; color:var(--bg-deep);"><?= htmlspecialchars($movie['movie_name']) ?></h2>
                    <span style="font-size:1.3rem; font-weight:600; color:var(--bg-deep);">LUMIÈRE</span>
                </div>
                <div class="divider" style="margin:15px 0; background:linear-gradient(90deg, transparent, var(--mocha), transparent);"></div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; font-size:1.1rem; color:var(--bg-deep);">
                    <div>
                        <div style="color:var(--mocha); font-style:italic; font-size:0.9rem;">Date</div>
                        <div style="font-weight:600;" id="ticketDate">-</div>
                    </div>
                    <div>
                        <div style="color:var(--mocha); font-style:italic; font-size:0.9rem;">Time</div>
                        <div style="font-weight:600;" id="ticketTime">-</div>
                    </div>
                    <div>
                        <div style="color:var(--mocha); font-style:italic; font-size:0.9rem;">Seats</div>
                        <div style="font-weight:600;" id="ticketSeats">-</div>
                    </div>
                    <div>
                        <div style="color:var(--mocha); font-style:italic; font-size:0.9rem;">Tier</div>
                        <div style="font-weight:600;"><?= htmlspecialchars($tier) ?></div>
                    </div>
                </div>
            </div>
            <div class="e-ticket-stub">
                <h3 style="font-size:1.4rem; color:var(--bg-deep);">ADMIT ONE</h3>
                <div style="width:90px; height:90px; border:2px solid var(--bg-deep); display:flex; justify-content:center; align-items:center; margin-top:15px; overflow:hidden;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data=https://youtu.be/QDia3e12czc" alt="QR Code" style="width:100%; height:100%;">
                </div>
                <p style="margin-top:15px; font-style:italic; font-size:0.85rem; color:var(--bg-deep);">Keep this stub</p>
            </div>
        </div>
    </div>

    <script src="js/main.js?v=5"></script>
    <script>
    //  State
    const MOVIE_ID      = <?= intval($mid) ?>;
    const PRICE         = <?= LUMIERE_BASE_PRICE * $price_multiplier ?>;
    const SELECTED_TIER = "<?= htmlspecialchars($tier) ?>";
    const takenSeatsDB  = <?= json_encode($taken_seats_data) ?>;

    let selectedDate     = '';
    let selectedShowTime = '';
    let selectedSeats    = [];
    let appliedDiscount  = 0;
    let appliedPromoId   = null;
    let appliedPromoCode = '';

    //  Date-Time selectors
    function selectDate(date, btn) {
        document.querySelectorAll('.date-btn').forEach(b => {
            b.style.background  = 'rgba(212,168,83,0.05)';
            b.style.borderColor = 'rgba(212,168,83,0.2)';
            b.style.color       = 'var(--cream)';
        });
        btn.style.background  = 'var(--gold)';
        btn.style.color       = 'var(--bg-dark)';
        btn.style.borderColor = 'var(--gold)';
        selectedDate = date;
        document.getElementById('selectedDate').value = date;
        if (selectedDate && selectedShowTime) renderSeatMap();
    }

    function selectTime(time, btn) {
        document.querySelectorAll('.showtime-btn').forEach(b => {
            b.style.background = 'rgba(212,168,83,0.05)';
            b.style.color      = 'var(--cream)';
        });
        btn.style.background = 'var(--sunset-coral)';
        btn.style.color      = 'white';
        selectedShowTime = time;
        document.getElementById('selectedShowTime').value = time;
        if (selectedDate && selectedShowTime) renderSeatMap();
    }

    window.addEventListener('DOMContentLoaded', () => {
        document.querySelector('.date-btn')?.click();
        document.querySelector('.showtime-btn')?.click();
    });

    //  Seat Map
    const map  = document.getElementById('seatMap');
    const rows = ['A','B','C','D','E','F'];
    const cols = 12;

    function renderSeatMap() {
        map.innerHTML = '';
        selectedSeats = [];
        updateSummary();

        const key          = selectedDate + '_' + selectedShowTime.substring(0, 8);
        const takenForSlot = takenSeatsDB[key] || [];

        rows.forEach(row => {
            const rd = document.createElement('div');
            rd.className = 'seat-row';

            const l = document.createElement('div');
            l.className   = 'row-label';
            l.textContent = row;
            rd.appendChild(l);

            for (let i = 1; i <= cols; i++) {
                const seatId = `${row}${i}`;
                const s      = document.createElement('div');
                if (takenForSlot.includes(seatId)) {
                    s.className = 'seat taken';
                } else {
                    s.className  = 'seat available';
                    s.dataset.id = seatId;
                    s.onclick    = () => toggleSeat(s, seatId);
                }
                rd.appendChild(s);
            }

            const le = document.createElement('div');
            le.className   = 'row-label';
            le.textContent = row;
            rd.appendChild(le);
            map.appendChild(rd);
        });
    }

    function toggleSeat(el, id) {
        if (el.classList.contains('selected')) {
            el.classList.replace('selected', 'available');
            selectedSeats = selectedSeats.filter(s => s !== id);
        } else {
            el.classList.replace('available', 'selected');
            selectedSeats.push(id);
        }
        updateSummary();
    }

    //  Price calculations
    function getSubtotal()   { return selectedSeats.length * PRICE; }
    function getFinalTotal() { return Math.max(0, getSubtotal() - appliedDiscount); }

    function updateSummary() {
        const list = document.getElementById('selectionList');
        const btn  = document.getElementById('step1Btn');

        list.innerHTML = '';

        if (selectedSeats.length === 0) {
            list.innerHTML = '<li><span style="color:var(--mocha); font-style:italic;">No seats selected</span></li>';
            document.getElementById('totalPrice').textContent = '€ 0.00';
            btn.style.opacity       = '0.5';
            btn.style.pointerEvents = 'none';
            document.getElementById('discountLine').classList.remove('visible');
            return;
        }

        btn.style.opacity       = '1';
        btn.style.pointerEvents = 'auto';

        selectedSeats.forEach(s => {
            const li = document.createElement('li');
            li.innerHTML = `<span>Seat ${s}</span><span>€${PRICE.toFixed(2)}</span>`;
            list.appendChild(li);
        });

        if (appliedDiscount > 0) {
            document.getElementById('discountLine').classList.add('visible');
            document.getElementById('discountAmount').textContent = `- € ${appliedDiscount.toFixed(2)}`;
        } else {
            document.getElementById('discountLine').classList.remove('visible');
        }

        document.getElementById('totalPrice').textContent  = `€ ${getFinalTotal().toFixed(2)}`;
        document.getElementById('ticketSeats').textContent = selectedSeats.join(', ');

        document.getElementById('paySubtotal').textContent = `€ ${getSubtotal().toFixed(2)}`;
        document.getElementById('payTotal').textContent    = `€ ${getFinalTotal().toFixed(2)}`;
        const payDiscRow = document.getElementById('payDiscountRow');
        if (appliedDiscount > 0) {
            payDiscRow.style.display = 'flex';
            document.getElementById('payDiscountAmt').textContent = `- € ${appliedDiscount.toFixed(2)}`;
        } else {
            payDiscRow.style.display = 'none';
        }
    }

    //  Step navigation
    function nextStep(s) {
        if (s === 2 && selectedSeats.length === 0) return;

        document.querySelectorAll('.step-content').forEach(c => c.classList.remove('active'));
        document.getElementById(`step${s}`).classList.add('active');

        document.querySelectorAll('.step-dot').forEach((d, i) => {
            if (i < s) d.classList.add('active');
            else       d.classList.remove('active');
        });

        for (let n = 1; n <= 3; n++) {
            const line = document.getElementById('line' + n);
            if (line) {
                if (n < s) line.classList.add('active');
                else       line.classList.remove('active');
            }
        }

        const blueprint = document.querySelector('.theatre-blueprint');
        if (blueprint) {
            if (s > 1) {
                blueprint.style.pointerEvents = 'none';
                blueprint.style.opacity       = '0.5';
                blueprint.style.filter        = 'grayscale(30%)';
                blueprint.style.transition    = 'all 0.4s ease';
            } else {
                blueprint.style.pointerEvents = 'auto';
                blueprint.style.opacity       = '1';
                blueprint.style.filter        = 'none';
            }
        }

        if (s === 4) updateSummary();
    }

    //  Promo Code logic: calls api_validate_promo.php
    function onPromoInput() {
        const val = document.getElementById('promoCodeInput').value.trim();
        document.getElementById('applyPromoBtn').disabled = val.length < 2;
        hideFeedback();
    }

    function hideFeedback() {
        document.getElementById('promoFeedback').className = 'promo-feedback';
    }

    function showFeedback(type, title, body) {
        const fb = document.getElementById('promoFeedback');
        fb.className = 'promo-feedback ' + type;
        document.getElementById('promoIcon').textContent          = type === 'success' ? '✓' : '✕';
        document.getElementById('promoFeedbackTitle').textContent = title;
        document.getElementById('promoFeedbackBody').textContent  = body;
    }

    async function applyPromo() {
        const code     = document.getElementById('promoCodeInput').value.trim().toUpperCase();
        const subtotal = getSubtotal();
        if (!code) return;

        const btn = document.getElementById('applyPromoBtn');
        btn.textContent = '…';
        btn.disabled    = true;

        try {
            const url  = `api_validate_promo.php?code=${encodeURIComponent(code)}&total=${subtotal}`;
            const res  = await fetch(url);
            const data = await res.json();

            if (data.success) {
                appliedDiscount  = parseFloat(data.discount);
                appliedPromoId   = data.promotion_id;
                appliedPromoCode = code;

                showFeedback('success', 'Promo applied!',
                    data.description || `You save € ${appliedDiscount.toFixed(2)}.`);

                document.getElementById('promoBadgeText').textContent =
                    `${code} - € ${appliedDiscount.toFixed(2)}`;
                document.getElementById('promoBadgeRow').style.display = 'block';

                document.getElementById('promoCodeInput').disabled = true;
                btn.style.display = 'none';

                updateSummary();
            } else {
                showFeedback('error', 'Code not valid', data.error || 'Please try another code.');
                btn.textContent = 'Apply';
                btn.disabled    = false;
            }
        } catch (err) {
            showFeedback('error', 'Connection error', 'Could not validate code. Please try again.');
            btn.textContent = 'Apply';
            btn.disabled    = false;
        }
    }

    function removePromo() {
        appliedDiscount  = 0;
        appliedPromoId   = null;
        appliedPromoCode = '';

        const input = document.getElementById('promoCodeInput');
        input.value    = '';
        input.disabled = false;

        const btn = document.getElementById('applyPromoBtn');
        btn.textContent   = 'Apply';
        btn.disabled      = true;
        btn.style.display = '';

        document.getElementById('promoBadgeRow').style.display = 'none';
        hideFeedback();
        updateSummary();
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('promoCodeInput').addEventListener('keydown', e => {
            if (e.key === 'Enter') { e.preventDefault(); applyPromo(); }
        });
    });

    //  Payment form submit: calls api_create_order.php
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('paymentForm').onsubmit = e => {
            e.preventDefault();

            const expiry = document.getElementById('cc_expiry').value.trim();
            const match  = expiry.match(/^(0[1-9]|1[0-2])\/([0-9]{2})$/);

            if (!match) { alert('Please enter a valid expiry date in MM/YY format.'); return; }

            const month    = parseInt(match[1], 10);
            const year     = parseInt('20' + match[2], 10);
            const now      = new Date();

            if (year < now.getFullYear() || (year === now.getFullYear() && month < now.getMonth() + 1)) {
                alert('Your credit card has expired.'); return;
            }

            document.getElementById('payBtn').textContent = 'Processing…';

            const formData = new FormData();
            formData.append('movie_id',     MOVIE_ID);
            formData.append('show_date',    selectedDate);
            formData.append('show_time',    selectedShowTime);
            formData.append('seats',        selectedSeats.join(', '));
            formData.append('n_seats',      selectedSeats.length);
            formData.append('price',        getFinalTotal().toFixed(2));
            formData.append('tier',         SELECTED_TIER);
            formData.append('cc_number',    document.getElementById('cc_number').value);
            formData.append('cc_expiry',    document.getElementById('cc_expiry').value);
            formData.append('cc_cvc',       document.getElementById('cc_cvc').value);
            if (appliedPromoId) formData.append('promotion_id', appliedPromoId);

            fetch('api_create_order.php', { method: 'POST', body: formData })
                .then(r => r.text().then(text => {
                    try { return JSON.parse(text); }
                    catch { throw new Error('Server error: ' + text.substring(0, 300)); }
                }))
                .then(data => {
                    if (data.success) {
                        const dateObj       = new Date(selectedDate);
                        const formattedDate = dateObj.toLocaleDateString('en-GB', {
                            weekday: 'short', day: 'numeric', month: 'short', year: 'numeric'
                        });
                        document.getElementById('ticketDate').textContent = formattedDate;
                        document.getElementById('ticketTime').textContent = selectedShowTime.substring(0, 5);

                        setTimeout(() => {
                            const m = document.getElementById('ticketModal');
                            m.classList.add('active');
                            setTimeout(() => m.classList.add('show'), 50);
                            setTimeout(() => triggerPageTransition('history.php'), 5000);
                        }, 1000);
                    } else {
                        alert('Booking failed: ' + (data.error || 'Unknown error'));
                        document.getElementById('payBtn').textContent = 'Complete Reservation';
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert(err.message || 'An error occurred while processing your reservation.');
                    document.getElementById('payBtn').textContent = 'Complete Reservation';
                });
        };
    });
    </script>
</body>
</html>