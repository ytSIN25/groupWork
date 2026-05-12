<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index_login.php');
    exit();
}

// Contact Form Handler
$contact_success = false;
$contact_error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name    = trim($_POST['contact_name']    ?? '');
    $email   = trim($_POST['contact_email']   ?? '');
    $subject = trim($_POST['contact_subject'] ?? '');
    $message = trim($_POST['contact_message'] ?? '');
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$name || !$email || !$subject || !$message) {
        $contact_error = 'Please fill in all fields before sending.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $contact_error = 'Please enter a valid email address.';
    } else {
        $stmt = $conn->prepare("
            INSERT INTO contact_messages (user_id, name, email, subject, message)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('issss', $user_id, $name, $email, $subject, $message);
        if ($stmt->execute()) {
            $contact_success = true;
        } else {
            $contact_error = 'Something went wrong. Please try again shortly.';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Learn about the heritage and restoration of LUMIÈRE Cinemas.">
    <title>LUMIÈRE - Our Philosophy &amp; Heritage</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/pages/index.css">
    <link rel="stylesheet" href="css/pages/footer.css">
    <link rel="stylesheet" href="css/global.css">
</head>
<body>
    <div class="film-grain"></div>
    <div class="page-transition active" id="pageTransition">
        <span class="trans-logo">LUMIÈRE</span>
    </div>

    <!-- Navigation -->
    <nav class="lumiere-nav">
        <a href="index.php" class="lumiere-logo" data-no-transition>
            <img src="assets/images/logo.svg" alt="LUMIÈRE">
        </a>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Home</a>
            <a href="movies.php" class="nav-link">Now Showing</a>
            <a href="history.php" class="nav-link">My Tickets</a>
            <a href="dashboard_user.php" class="nav-link">Account</a>
            <a href="about.php" class="nav-link" style="color:var(--sunset-coral); border-bottom: 1.5px solid var(--sunset-coral);">The Cinema</a>
        </div>
    </nav>

    <div class="page-wrapper">

        <!-- ---------- Hero ---------- -->
        <header class="about-hero">
            <img src="assets/images/hero-bg.png" alt="Cinema Hall">
            <div class="about-title-wrapper reveal">
                <span class="about-tag">Established 1922</span>
                <h1 class="about-title">A Sanctuary for Cinema</h1>
                <div class="about-subtitle">Where every frame tells a story</div>
            </div>
        </header>

        <!-- ---------- Philosophy ---------- -->
        <section class="about-philosophy reveal">
            <p><span class="dropcap">T</span>o sit in the dark and witness light paint dreams upon a wall - this is the magic we curate. At LUMIÈRE, we believe that cinema is not merely a sequence of frames, but a visceral, communal experience that has the power to transcend time. Our theatre was founded by visionaries whose very name became synonymous with the birth of moving pictures.</p>
            <p>We are not a multiplex. We are a temple of storytelling, dedicated to those who appreciate the flicker of a 35mm projector, the rich velvety acoustics of a classic acoustic hall, and the deep, shared silence of an audience held captive by a masterpiece.</p>
        </section>

        <!-- ---------- Legacy / History ---------- -->
        <section class="legacy-section">
            <div class="legacy-grid reveal">
                <div class="legacy-content">
                    <h2>The Golden Age Restored</h2>
                    <p>In 2024, LUMIÈRE underwent a meticulous three-year restoration. We worked with master craftsmen to reupholster every velvet seat with fabric commissioned from a historic mill in Northern Italy. The breathtaking art deco murals, once hidden behind layers of modern whitewash, have been lovingly brought back to life, shimmering once more in their original gold-leaf splendour.</p>
                    <p>From the brass ticketing booths to the opulent chandelier that crowns the main auditorium, every detail has been resurrected to transport you back to the golden age of Hollywood.</p>
                </div>
                <div class="legacy-image-wrapper">
                    <img src="https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?q=80&w=1200&auto=format&fit=crop" alt="Restored Cinema Interior" class="legacy-image bloom">
                </div>
            </div>
        </section>

        <!-- ---------- Stats ---------- -->
        <section class="stats-showcase" id="statsBox">
            <div class="stat-card reveal" style="animation-delay: 0.1s;">
                <div class="stat-num" id="statFilms">0</div>
                <div class="stat-title">Celluloid Dreams Screened</div>
            </div>
            <div class="stat-card reveal" style="animation-delay: 0.3s;">
                <div class="stat-num" id="statSeats">0</div>
                <div class="stat-title">Patrons Captivated</div>
            </div>
            <div class="stat-card reveal" style="animation-delay: 0.5s;">
                <div class="stat-num" id="statYears">0</div>
                <div class="stat-title">Years of Cinematic History</div>
            </div>
        </section>

        <!-- ---------- Projection Room ---------- -->
        <section class="legacy-section" style="background: var(--bg-deep); border: none;">
            <div class="legacy-grid reveal">
                <div class="legacy-image-wrapper">
                    <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=1200&auto=format&fit=crop" alt="Twin 35mm Projectors" class="legacy-image bloom" style="filter: sepia(0.6) grayscale(0.5) contrast(1.2);">
                </div>
                <div class="legacy-content">
                    <h2>The Heart of the Hall</h2>
                    <p>But the true heart of our restoration was the projection hall. While we possess state-of-the-art 4K laser projection for contemporary masterpieces, we have proudly preserved and serviced our twin 35mm carbon arc projectors.</p>
                    <p>There is a texture, a warmth, and a soul in film that digital simply cannot replicate - a grain that breathes with every passing second. For special retrospectives and classic re-releases, we still project exactly as the directors originally intended: on glorious, humming 35mm celluloid.</p>
                    <a href="movies.php" class="btn-primary" style="margin-top: 30px;">Discover the Program</a>
                </div>
            </div>
        </section>

        <!-- ---------- FAQ ---------- -->
        <section class="faq-section" id="faq">
            <div class="section-header reveal">
                <span class="about-tag">Know Before You Go</span>
                <h2>Frequently Asked Questions</h2>
                <p>Everything you need to know about an evening at LUMIÈRE.</p>
            </div>

            <div class="faq-list reveal">

                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-question-text">How do I book tickets at LUMIÈRE?</span>
                        <span class="faq-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </span>
                    </button>
                    <div class="faq-answer">
                        <p>You can reserve your seats directly through our <a href="movies.php">Now Showing</a> page. Simply choose your film, preferred showtime, and select your seats on the interactive plan. Tickets are confirmed immediately and linked to your account.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-question-text">What are the different seating tiers?</span>
                        <span class="faq-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </span>
                    </button>
                    <div class="faq-answer">
                        <p>LUMIÈRE offers three curated seating experiences. <span style="color:var(--gold)">The Stalls</span> places you at the heart of the action in our orchestra level. <span style="color:var(--gold)">The Circle</span> offers an elevated, unobstructed perspective from the mezzanine. <span style="color:var(--gold)">The Royal Box</span> provides our most panoramic view of both the screen and the hall's magnificent restored architecture.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-question-text">Can I use a promo code when purchasing?</span>
                        <span class="faq-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </span>
                    </button>
                    <div class="faq-answer">
                        <p>Absolutely. Promo codes are applied at the checkout step during booking. Each code carries its own minimum spend and discount value, which is displayed before confirmation. Codes cannot be combined and are non-transferable.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-question-text">Do you still screen 35mm film?</span>
                        <span class="faq-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </span>
                    </button>
                    <div class="faq-answer">
                        <p>Yes, and we consider it one of our greatest honours. Our twin 35mm carbon arc projectors are fully operational and used for classic retrospectives and special re-release events throughout the year. These evenings sell out quickly, so keep a close eye on the programme.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-question-text">What is the dress code?</span>
                        <span class="faq-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </span>
                    </button>
                    <div class="faq-answer">
                        <p>There is no formal requirement, though we encourage patrons to treat an evening at LUMIÈRE as a special occasion. Smart-casual attire reflects the spirit of our house, an atmosphere of refined appreciation rather than casual convenience.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-question-text">How can I cancel or change my booking?</span>
                        <span class="faq-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </span>
                    </button>
                    <div class="faq-answer">
                        <p>All bookings can be viewed in <a href="history.php">My Tickets</a>. At this time, amendments must be made by contacting our house directly via the form below, at least 48 hours before the scheduled performance. Cancellations within 24 hours of the screening are non-refundable.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-question-text">Is the auditorium accessible for patrons with disabilities?</span>
                        <span class="faq-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </span>
                    </button>
                    <div class="faq-answer">
                        <p>LUMIÈRE is fully accessible. Our restoration included the installation of a heritage lift, accessible restrooms, and dedicated wheelchair spaces with companion seating in all three tiers. Induction loop systems are installed throughout the main auditorium. Please contact us in advance if you require any additional assistance.</p>
                    </div>
                </div>

            </div>
        </section>

        <!-- ---------- Contact Section ---------- -->
        <section class="contact-section" id="contact">
            <div class="contact-inner">

                <!-- ----- Left: Info ----- -->
                <div class="contact-info reveal">
                    <span class="about-tag" style="margin-bottom: 1.5rem; display: inline-block;">Write to Us</span>
                    <h3>Send a Message<br>to the House</h3>
                    <p>Whether you have a question about our programme, wish to make a special arrangement, or simply want to share your experience, we read every letter personally.</p>

                    <div class="contact-detail">
                        <div class="contact-detail-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                <circle cx="12" cy="9" r="2.5"/>
                            </svg>
                        </div>
                        <div class="contact-detail-text">
                            <strong>Address</strong>
                            <span>12 Grand Boulevard, The Old Quarter</span>
                        </div>
                    </div>

                    <div class="contact-detail">
                        <div class="contact-detail-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2"/>
                                <polyline points="2,4 12,13 22,4"/>
                            </svg>
                        </div>
                        <div class="contact-detail-text">
                            <strong>Electronic Post</strong>
                            <span>hello@lumiere.com</span>
                        </div>
                    </div>

                    <div class="contact-detail">
                        <div class="contact-detail-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12,6 12,12 16,14"/>
                            </svg>
                        </div>
                        <div class="contact-detail-text">
                            <strong>Box Office Hours</strong>
                            <span>Daily · 12:00 — 22:30</span>
                        </div>
                    </div>
                </div>

                <!-- ----- Right: Form ----- -->
                <div class="contact-form-wrapper reveal" style="animation-delay: 0.2s;">
                    <div class="contact-form">

                        <?php if ($contact_success): ?>
                        <!-- -- Success state -- -->
                        <div class="contact-success-state">
                            <div class="success-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                            <h4>Message Received</h4>
                            <p>Thank you, <?= htmlspecialchars($_POST['contact_name'] ?? 'dear patron') ?>.<br>We will respond within one to two business days.</p>
                        </div>

                        <?php else: ?>
                        <!-- -- Form -- -->
                        <form method="POST" action="#contact" novalidate>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="contact_name">Your Name</label>
                                    <input
                                        type="text"
                                        id="contact_name"
                                        name="contact_name"
                                        placeholder="e.g. Isabelle Fontaine"
                                        value="<?= htmlspecialchars($_POST['contact_name'] ?? '') ?>"
                                        required
                                    >
                                </div>
                                <div class="form-group">
                                    <label for="contact_email">Email Address</label>
                                    <input
                                        type="email"
                                        id="contact_email"
                                        name="contact_email"
                                        placeholder="you@example.com"
                                        value="<?= htmlspecialchars($_POST['contact_email'] ?? '') ?>"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="contact_subject">Subject</label>
                                <select style="font-size: 0.95rem;" id="contact_subject" name="contact_subject" required>
                                    <option value="" disabled <?= empty($_POST['contact_subject']) ? 'selected' : '' ?>>Select a topic…</option>
                                    <option value="General Enquiry"     <?= ($_POST['contact_subject'] ?? '') === 'General Enquiry'     ? 'selected' : '' ?>>General Enquiry</option>
                                    <option value="Booking Assistance"  <?= ($_POST['contact_subject'] ?? '') === 'Booking Assistance'  ? 'selected' : '' ?>>Booking Assistance</option>
                                    <option value="Cancellation Request"<?= ($_POST['contact_subject'] ?? '') === 'Cancellation Request'? 'selected' : '' ?>>Cancellation Request</option>
                                    <option value="Private Hire"        <?= ($_POST['contact_subject'] ?? '') === 'Private Hire'        ? 'selected' : '' ?>>Private Hire Enquiry</option>
                                    <option value="Programme Suggestion"<?= ($_POST['contact_subject'] ?? '') === 'Programme Suggestion'? 'selected' : '' ?>>Programme Suggestion</option>
                                    <option value="Accessibility"       <?= ($_POST['contact_subject'] ?? '') === 'Accessibility'       ? 'selected' : '' ?>>Accessibility &amp; Special Needs</option>
                                    <option value="Other"               <?= ($_POST['contact_subject'] ?? '') === 'Other'               ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="contact_message">Your Message</label>
                                <textarea
                                    id="contact_message"
                                    name="contact_message"
                                    placeholder="Write your message here…"
                                    required
                                ><?= htmlspecialchars($_POST['contact_message'] ?? '') ?></textarea>
                            </div>

                            <div class="form-submit-row">
                                <button type="submit" name="contact_submit" class="btn-primary">Send Message</button>
                                <?php if ($contact_error): ?>
                                <div class="form-feedback error"><?= $contact_error ?></div>
                                <?php endif; ?>
                            </div>

                        </form>
                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </section>

        <!-- ---------- Footer ---------- -->
        <footer>
            <img src="assets/images/logo.svg" alt="LUMIÈRE" class="logo-img">
            <p>Where Every Seat Tells a Story.</p>
            <div class="footer-links" style="margin-top: 25px;">
                <a href="movies.php">Now Showing</a>
                <a href="about.php">The Cinema</a>
                <a href="dashboard_user.php">Account</a>
                <a href="dashboard_admin.php">Staff Area</a>
            </div>
            <p style="margin-top:30px; font-size:0.9rem; opacity:0.5;">© 2026 LUMIÈRE Cinemas. All rights reserved.</p>
        </footer>

    </div>

    <script src="js/main.js"></script>
    <script>
        // Stats Counter Animation
        function animateCounter(el, target, dur) {
            if (!el) return;
            let start = 0;
            const step = target / ((dur / 1000) * 60);
            function tick() {
                start += step;
                if (start >= target) {
                    el.textContent = target.toLocaleString() + "+";
                    return;
                }
                el.textContent = Math.floor(start).toLocaleString();
                requestAnimationFrame(tick);
            }
            tick();
        }
        const obs = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    animateCounter(document.getElementById('statFilms'), 14500, 2500);
                    animateCounter(document.getElementById('statSeats'), 342000, 3000);
                    animateCounter(document.getElementById('statYears'), 104, 2000);
                    obs.disconnect();
                }
            });
        }, { threshold: 0.5 });
        setTimeout(() => {
            const statsBox = document.getElementById('statsBox');
            if (statsBox) obs.observe(statsBox);
        }, 1000);

        // FAQ Accordion
        document.querySelectorAll('.faq-question').forEach(btn => {
            btn.addEventListener('click', () => {
                const item   = btn.closest('.faq-item');
                const isOpen = item.classList.contains('open');

                // Close all
                document.querySelectorAll('.faq-item.open').forEach(openItem => {
                    openItem.classList.remove('open');
                    openItem.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
                });

                // Open clicked (unless it was already open)
                if (!isOpen) {
                    item.classList.add('open');
                    btn.setAttribute('aria-expanded', 'true');
                }
            });
        });
    </script>
</body>
</html>