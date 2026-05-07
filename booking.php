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
  <style>.step-content{display:none;}.step-content.active{display:block;animation:stepIn 0.5s ease;} @keyframes stepIn{from{opacity:0;transform:translateY(15px)}to{opacity:1;transform:translateY(0)}}</style>
</head>
<body>
  
  <div class="film-grain"></div>
  <div class="page-transition active" id="pageTransition"><span class="trans-logo">LUMIÈRE</span></div>
  <nav class="lumiere-nav">
    <a href="movies.php" class="lumiere-logo"><img src="assets/images/logo.svg?v=5" alt="LUMIÈRE"></a>
    <div class="nav-links"><a href="movies.php" class="nav-link">Cancel Reservation</a></div>
  </nav>

  <div class="page-wrapper seat-booking-wrapper">
    <div class="theatre-blueprint fade-up">
      <div class="screen-curve">THE SILVER SCREEN</div>
      <div class="seat-map" id="seatMap"></div>
      <div class="seat-legend">
        <div class="legend-item"><div class="seat available" style="cursor:default;"></div> Available</div>
        <div class="legend-item"><div class="seat taken" style="cursor:default;"></div> Reserved</div>
        <div class="legend-item"><div class="seat selected" style="cursor:default;"></div> Your Selection</div>
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
        <h3 style="color:var(--cream); margin-bottom:20px; font-family:var(--font-accent);">Oppenheimer</h3>
        <ul class="booking-summary-list" id="selectionList"><li><span style="color:var(--mocha); font-style:italic;">No seats selected</span></li></ul>
        <ul class="booking-summary-list" style="border-top:1px solid rgba(212,168,83,0.1);"><li class="booking-total">Total <span id="totalPrice">£0.00</span></li></ul>
        <button class="btn-primary" style="width:100%; margin-top:10px; opacity:0.5; pointer-events:none;" id="step1Btn" onclick="nextStep(2)">Continue</button>
      </div>
      <div id="step2" class="step-content">
        <h2 style="margin-bottom:20px;">Patron Details</h2>
        <form onsubmit="event.preventDefault(); nextStep(3);">
          <div class="form-group"><label>Name</label><input type="text" value="Arthur Shelby" required></div>
          <div class="form-group"><label>Email</label><input type="email" value="arthur@example.com" required></div>
          <button class="btn-primary" style="width:100%; margin-top:10px;" type="button" onclick="nextStep(1)">Back</button>
          <button class="btn-coral" style="width:100%; margin-top:10px;" type="submit">Continue to Payment</button>
        </form>
      </div>
      <div id="step3" class="step-content">
        <h2 style="margin-bottom:20px;">Settle Account</h2>
        <form id="paymentForm">
          <div class="form-group"><label>Card Number</label><input type="text" placeholder="XXXX XXXX XXXX XXXX" required></div>
          <div style="display:flex; gap:15px;"><div class="form-group" style="flex:1;"><label>Expiry</label><input type="text" placeholder="MM/YY" required></div><div class="form-group" style="flex:1;"><label>CVC</label><input type="text" placeholder="123" required></div></div>
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
        <div style="display:flex; justify-content:space-between; align-items:flex-end;"><h2 style="font-size:2.2rem; font-style:italic; color:var(--bg-deep);">Oppenheimer</h2><span style="font-size:1.3rem; font-weight:600; color:var(--bg-deep);">LUMIÈRE</span></div>
        <div class="divider" style="margin:15px 0; background:linear-gradient(90deg, transparent, var(--mocha), transparent);"></div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; font-size:1.1rem; color:var(--bg-deep);">
          <div><div style="color:var(--mocha); font-style:italic; font-size:0.9rem;">Date</div><div style="font-weight:600;">Sat, 22 Mar 2026</div></div>
          <div><div style="color:var(--mocha); font-style:italic; font-size:0.9rem;">Time</div><div style="font-weight:600;">19:30</div></div>
          <div><div style="color:var(--mocha); font-style:italic; font-size:0.9rem;">Seats</div><div style="font-weight:600;" id="ticketSeats">-</div></div>
          <div><div style="color:var(--mocha); font-style:italic; font-size:0.9rem;">Tier</div><div style="font-weight:600;">Stalls</div></div>
        </div>
      </div>
      <div class="e-ticket-stub">
        <h3 style="font-size:1.4rem; color:var(--bg-deep);">ADMIT ONE</h3>
        <div style="width:90px; height:90px; border:2px solid var(--bg-deep); display:flex; justify-content:center; align-items:center; margin-top:15px;">
          <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#0D0B0E" stroke-width="1.5"><path d="M3 3h6v6H3zM15 3h6v6h-6zM3 15h6v6H3z"/><rect x="5" y="5" width="2" height="2"/><rect x="17" y="5" width="2" height="2"/><rect x="5" y="17" width="2" height="2"/><path d="M15 15h6v6h-6z"/><rect x="17" y="17" width="2" height="2"/><path d="M9 9h6v6H9z"/></svg>
        </div>
        <p style="margin-top:15px; font-style:italic; font-size:0.85rem; color:var(--bg-deep);">Keep this stub</p>
      </div>
    </div>
  </div>

  <script src="js/main.js?v=5"></script>
  <script>
    const map=document.getElementById('seatMap'),rows=['A','B','C','D','E','F'],cols=12;let selectedSeats=[],PRICE=12;
    rows.forEach(row=>{const rd=document.createElement('div');rd.className='seat-row';const l=document.createElement('div');l.className='row-label';l.textContent=row;rd.appendChild(l);
    for(let i=1;i<=cols;i++){const s=document.createElement('div');const taken=Math.random()>0.8;if(taken){s.className='seat taken';}else{s.className='seat available';s.dataset.id=`${row}${i}`;s.onclick=()=>toggleSeat(s,`${row}${i}`);}rd.appendChild(s);}
    const le=document.createElement('div');le.className='row-label';le.textContent=row;rd.appendChild(le);map.appendChild(rd);});
    function toggleSeat(el,id){if(el.classList.contains('selected')){el.classList.remove('selected');el.classList.add('available');selectedSeats=selectedSeats.filter(s=>s!==id);}else{el.classList.remove('available');el.classList.add('selected');selectedSeats.push(id);}updateSummary();}
    function updateSummary(){const list=document.getElementById('selectionList'),total=document.getElementById('totalPrice'),btn=document.getElementById('step1Btn');list.innerHTML='';
    if(selectedSeats.length===0){list.innerHTML='<li><span style="color:var(--mocha); font-style:italic;">No seats selected</span></li>';total.textContent='£0.00';btn.style.opacity='0.5';btn.style.pointerEvents='none';return;}
    btn.style.opacity='1';btn.style.pointerEvents='auto';selectedSeats.forEach(s=>{const li=document.createElement('li');li.innerHTML=`<span>Seat ${s}</span><span>£${PRICE}.00</span>`;list.appendChild(li);});
    total.textContent=`£${(selectedSeats.length*PRICE).toFixed(2)}`;document.getElementById('ticketSeats').textContent=selectedSeats.join(', ');}
    updateSummary();
    function nextStep(s){if(s===2&&selectedSeats.length===0)return;document.querySelectorAll('.step-content').forEach(c=>c.classList.remove('active'));document.getElementById(`step${s}`).classList.add('active');document.querySelectorAll('.step-dot').forEach((d,i)=>{if(i<s)d.classList.add('active');else d.classList.remove('active');});}
    document.getElementById('paymentForm').onsubmit=e=>{e.preventDefault();document.getElementById('payBtn').textContent='Processing...';setTimeout(()=>{const m=document.getElementById('ticketModal');m.classList.add('active');setTimeout(()=>m.classList.add('show'),50);setTimeout(()=>triggerPageTransition('history.php'),5000);},1500);};
  </script>
</body>
</html>
