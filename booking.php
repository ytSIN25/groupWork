<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index_login.php');
    exit();
}

$mid = $_GET['movie_id'] ?? 1;
$stmt = $conn->prepare("SELECT * FROM movies WHERE movie_id = ?");
$stmt->bind_param("i", $mid);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();
$tier = $_GET['tier'] ?? 'Stalls';
$price_multiplier = 1.0;
if ($tier === 'Circle') $price_multiplier = 1.5;
elseif ($tier === 'Royal Box') $price_multiplier = 3.0;

if (!$movie) {
    die("Error: Movie not found.");
}

// Fetch current user details
$u_stmt = $conn->prepare("SELECT name, email FROM users WHERE user_id = ?");
$u_stmt->bind_param("i", $_SESSION['user_id']);
$u_stmt->execute();
$current_user = $u_stmt->get_result()->fetch_assoc();

// Fetch showtime slots
$st_stmt = $conn->prepare("SELECT showtime_id, start_time FROM showtimes WHERE movie_id = ? ORDER BY start_time");
$st_stmt->bind_param("i", $mid);
$st_stmt->execute();
$time_slots = $st_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch all orders
$orders_stmt = $conn->prepare("
    SELECT show_date, show_time, seats 
    FROM orders 
    WHERE movie_id = ?
");
$orders_stmt->bind_param("i", $mid);
$orders_stmt->execute();
$orders_res = $orders_stmt->get_result();

$taken_seats_data = [];
while ($row = $orders_res->fetch_assoc()) {
    $key = $row['show_date'] . '_' . substr($row['show_time'], 0, 8);
    if (!isset($taken_seats_data[$key])) {
        $taken_seats_data[$key] = [];
    }
    // Split seats
    $seats_array = array_map('trim', explode(',', $row['seats']));
    $taken_seats_data[$key] = array_merge($taken_seats_data[$key], $seats_array);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $show_date = $_POST['show_date'] ?? '';
    $show_time = $_POST['show_time'] ?? '';

    if (empty($show_date) || empty($show_time)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Incomplete selection.']);
        exit;
    }

    $seats = $_POST['seats'] ?? '';
    $n_seats = $_POST['n_seats'] ?? 0;
    $price = $_POST['price'] ?? 0.00;
    $tier_to_save = $_POST['tier'] ?? 'Stalls';
    $cc_number = $_POST['cc_number'] ?? '';
    $cc_expiry = $_POST['cc_expiry'] ?? '';
    $cc_cvc = $_POST['cc_cvc'] ?? '';

    $stmt = $conn->prepare("INSERT INTO orders (user_id, movie_id, show_date, show_time, seats, num_seats, total_price, ticket_tier, cc_number, cc_expiry, cc_cvc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssidssss", $_SESSION['user_id'], $mid, $show_date, $show_time, $seats, $n_seats, $price, $tier_to_save, $cc_number, $cc_expiry, $cc_cvc);
    
    header('Content-Type: application/json');
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'order_id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    exit;
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
    <style>
        .step-content{display:none;}
        .step-content.active{display:block;animation:stepIn 0.5s ease;} 
        @keyframes stepIn{
            from{opacity:0;transform:translateY(15px)}
            to{opacity:1;transform:translateY(0)}
        }
        
        .date-scroll-container::-webkit-scrollbar { height: 8px; }
        .date-scroll-container::-webkit-scrollbar-track { background: rgba(212, 168, 83, 0.05); border-radius: 4px; }
        .date-scroll-container::-webkit-scrollbar-thumb { background: rgba(212, 168, 83, 0.3); border-radius: 4px; }
        .date-scroll-container::-webkit-scrollbar-thumb:hover { background: rgba(212, 168, 83, 0.6); }
        .date-scroll-container { scrollbar-width: thin; scrollbar-color: rgba(212, 168, 83, 0.3) rgba(212, 168, 83, 0.05); padding-bottom: 15px !important; }
    </style>
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
        <div class="theatre-blueprint fade-up">
            <div class="booking-selectors" style="margin-bottom:40px; display:flex; flex-direction:column; gap:20px; align-items:center;">
            <!-- Date Selector -->
            <div class="date-selector">
                <p style="font-family:var(--font-accent); color:var(--mocha); font-size:0.75rem; text-transform:uppercase; letter-spacing:0.2em; margin-bottom:10px; opacity:0.6;">1. Choose Date</p>
                <div class="date-scroll-container" style="display:flex; gap:10px; overflow-x:auto; padding:10px; width:100%; max-width:800px;">
                    <?php 
                    $start = new DateTime($movie['start_date']);
                    $today = new DateTime('today');
                    for($i=0; $i<14; $i++): 
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

            <!-- Time Selector -->
            <div class="time-selector">
                <p style="font-family:var(--font-accent); color:var(--mocha); font-size:0.75rem; text-transform:uppercase; letter-spacing:0.2em; margin-bottom:10px; opacity:0.6;">2. Choose Time</p>
                <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;" id="timeList">
                    <?php foreach($time_slots as $slot): ?>
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
                <div class="legend-item">
                    <div class="seat available" style="cursor:default;"></div> Available
                </div>
                <div class="legend-item">
                    <div class="seat taken" style="cursor:default;"></div> Reserved
                </div>
                <div class="legend-item">
                    <div class="seat selected" style="cursor:default;"></div> Your Selection
                </div>
            </div>
        </div>

        <div class="booking-panel fade-right">
            <div class="step-indicator">
                <div class="step-dot active" id="dot1">1</div>
                <div class="step-dot" id="dot2">2</div>
                <div class="step-dot" id="dot3">3</div>
            </div>

            <div id="step1" class="step-content active">
                <h2 style="margin-bottom:20px;">Reservation</h2>
                <h3><?= htmlspecialchars($movie['movie_name']) ?></h3>
                <ul class="booking-summary-list" id="selectionList">
                    <li><span style="color:var(--mocha); font-style:italic;">No seats selected</span></li>
                </ul>
                <ul class="booking-summary-list" style="border-top:1px solid rgba(212,168,83,0.1);">
                    <li class="booking-total">Total <span id="totalPrice">€ 0.00</span></li>
                </ul>
                <button class="btn-primary" style="width:100%; margin-top:10px; opacity:0.5; pointer-events:none;" id="step1Btn" onclick="nextStep(2)">Continue</button>
            </div>

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
                    <button class="btn-coral" style="width:100%; margin-top:10px;" type="submit">Continue to Payment</button>
                </form>
            </div>

            <div id="step3" class="step-content">
                <h2 style="margin-bottom:20px;">Settle Account</h2>
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
                    <button class="btn-primary" style="width:100%;" type="button" onclick="nextStep(2)">Back</button>
                    <button class="btn-coral" style="width:100%; margin-top:10px;" type="submit" id="payBtn">Complete Reservation</button>
                </form>
            </div>
        </div>
    </div>

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
                        <div style="font-weight:600;">Stalls</div>
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
        let selectedDate = '';
        let selectedShowTime = '';

        function selectDate(date, btn) {
            document.querySelectorAll('.date-btn').forEach(b => {
                b.style.background = 'rgba(212,168,83,0.05)';
                b.style.borderColor = 'rgba(212,168,83,0.2)';
                b.style.color = 'var(--cream)';
            });

            btn.style.background = 'var(--gold)';
            btn.style.color = 'var(--bg-dark)';
            btn.style.borderColor = 'var(--gold)';
            selectedDate = date;
            document.getElementById('selectedDate').value = date;
            if (selectedDate !== '' && selectedShowTime !== '') {
                renderSeatMap();
            }
        }

        function selectTime(time, btn) {
            document.querySelectorAll('.showtime-btn').forEach(b => {
                b.style.background = 'rgba(212,168,83,0.05)';
                b.style.color = 'var(--cream)';
            });
            
            btn.style.background = 'var(--sunset-coral)';
            btn.style.color = 'white';
            selectedShowTime = time;
            document.getElementById('selectedShowTime').value = time;
            if (selectedDate !== '' && selectedShowTime !== '') {
                renderSeatMap();
            }
        }

        // Initialize with first date and time
        window.addEventListener('DOMContentLoaded', () => {
            const firstDate = document.querySelector('.date-btn');
            if(firstDate) firstDate.click();
            const firstTime = document.querySelector('.showtime-btn');
            if(firstTime) firstTime.click();
        });

        const map = document.getElementById('seatMap');
        const rows = ['A','B','C','D','E','F'];
        const cols = 12;
        let selectedSeats = [];
        const PRICE = <?= LUMIERE_BASE_PRICE * $price_multiplier ?>;
        const SELECTED_TIER = "<?= htmlspecialchars($tier) ?>";
        
        // Seats
        const takenSeatsDB = <?= json_encode($taken_seats_data) ?>;

        function renderSeatMap() {
            map.innerHTML = '';
            selectedSeats = [];
            updateSummary();

            const key = selectedDate + '_' + selectedShowTime.substring(0, 8);
            const takenForSlot = takenSeatsDB[key] || [];

            rows.forEach(row => {
                const rd = document.createElement('div');
                rd.className = 'seat-row';
                const l = document.createElement('div');
                l.className = 'row-label';
                l.textContent = row;
                rd.appendChild(l);

                for (let i = 1; i <= cols; i++) {
                    const seatId = `${row}${i}`;
                    const s = document.createElement('div');
                    
                    if (takenForSlot.includes(seatId)) {
                        s.className = 'seat taken';
                    } else {
                        s.className = 'seat available';
                        s.dataset.id = seatId;
                        s.onclick = () => toggleSeat(s, seatId);
                    }
                    rd.appendChild(s);
                }

                const le = document.createElement('div');
                le.className = 'row-label';
                le.textContent = row;
                rd.appendChild(le);
                map.appendChild(rd);
            });
        }

        function toggleSeat(el,id){
            if(el.classList.contains('selected')){
                el.classList.remove('selected');
                el.classList.add('available');
                selectedSeats=selectedSeats.filter(s=>s!==id);
            }else{
                el.classList.remove('available');
                el.classList.add('selected');
                selectedSeats.push(id);}
                updateSummary();
        }

        function updateSummary(){
            const list=document.getElementById('selectionList');
            const total=document.getElementById('totalPrice');
            const btn=document.getElementById('step1Btn');
            list.innerHTML='';
            
            if(selectedSeats.length===0){
                list.innerHTML='<li><span style="color:var(--mocha); font-style:italic;">No seats selected</span></li>';
                total.textContent='RM0.00';
                btn.style.opacity='0.5';
                btn.style.pointerEvents='none';
                return;}
            btn.style.opacity='1';
            btn.style.pointerEvents='auto';
            selectedSeats.forEach(s=>{const li=document.createElement('li');li.innerHTML=`<span>Seat ${s}</span><span>€${PRICE}.00</span>`;list.appendChild(li);});
            total.textContent=`€${(selectedSeats.length*PRICE).toFixed(2)}`;
            document.getElementById('ticketSeats').textContent=selectedSeats.join(', ');}
            updateSummary();

        function nextStep(s){
            if(s===2&&selectedSeats.length===0)return;
            document.querySelectorAll('.step-content').forEach(c=>c.classList.remove('active'));
            document.getElementById(`step${s}`).classList.add('active');
            document.querySelectorAll('.step-dot').forEach((d,i)=>{if(i<s)d.classList.add('active');else d.classList.remove('active');});
            
            const blueprint = document.querySelector('.theatre-blueprint');
            if (blueprint) {
                if (s > 1) {
                    blueprint.style.pointerEvents = 'none';
                    blueprint.style.opacity = '0.5';
                    blueprint.style.filter = 'grayscale(30%)';
                    blueprint.style.transition = 'all 0.4s ease';
                } else {
                    blueprint.style.pointerEvents = 'auto';
                    blueprint.style.opacity = '1';
                    blueprint.style.filter = 'none';
                }
            }
        }
            
        document.getElementById('paymentForm').onsubmit = e => {
            e.preventDefault();
            const expiry = document.getElementById('cc_expiry').value.trim();
            const regex = /^(0[1-9]|1[0-2])\/([0-9]{2})$/;
            const match = expiry.match(regex);
            
            if (!match) {
                alert('Please enter a valid expiry date in MM/YY format.');
                return;
            }
            
            const month = parseInt(match[1], 10);
            const year = parseInt('20' + match[2], 10);
            const currentDate = new Date();
            const currentMonth = currentDate.getMonth() + 1;
            const currentYear = currentDate.getFullYear();
            
            if (year < currentYear || (year === currentYear && month < currentMonth)) {
                alert('Your credit card has expired.');
                return;
            }

            document.getElementById('payBtn').textContent = 'Processing...';

            const formData = new FormData();
            formData.append('show_date', selectedDate);
            formData.append('show_time', selectedShowTime);
            formData.append('seats', selectedSeats.join(', '));
            formData.append('n_seats', selectedSeats.length);
            formData.append('price', (selectedSeats.length * PRICE).toFixed(2));
            formData.append('tier', SELECTED_TIER);
            formData.append('cc_number', document.getElementById('cc_number').value);
            formData.append('cc_expiry', document.getElementById('cc_expiry').value);
            formData.append('cc_cvc', document.getElementById('cc_cvc').value);

            fetch('booking.php?movie_id=<?= $mid ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const dateObj = new Date(selectedDate);
                    const formattedDate = dateObj.toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
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
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your reservation.');
                document.getElementById('payBtn').textContent = 'Complete Reservation';
            });
        };
    </script>
</body>
</html>
