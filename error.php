<?php
// Fetch error details
$error_code = $_GET['code'] ?? '404';
$error_message = $_GET['msg'] ?? 'The Curtain Has Fallen Unexpectedly';
$error_details = $_GET['details'] ?? 'We couldn\'t find the scene you were looking for. It might have been left on the cutting room floor.';

// Map common codes to cinematic titles
$titles = [
    '404' => 'Scene Not Found',
    '403' => 'Private Screening Only',
    '500' => 'Projector Malfunction',
    '401' => 'Ticket Required',
    'db'  => 'Archive Connection Lost'
];
$display_title = $titles[$error_code] ?? 'Technical Difficulties';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUMIÈRE - <?= htmlspecialchars($display_title) ?></title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/global.css">
    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            background: radial-gradient(circle at center, rgba(178, 34, 34, 0.1) 0%, transparent 70%);
        }

        .error-card {
            max-width: 600px;
            width: 100%;
            text-align: center;
            animation: cinematic-reveal 1.2s var(--ease-smooth) forwards;
        }

        .error-code {
            font-size: 8rem;
            font-family: var(--font-display);
            color: var(--retro-red);
            line-height: 1;
            margin-bottom: 0;
            text-shadow: 0 0 30px rgba(178, 34, 34, 0.4);
            opacity: 0.8;
            letter-spacing: -0.05em;
        }

        .error-title {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-style: italic;
            color: var(--gold);
        }

        .error-message {
            font-size: 1.2rem;
            color: var(--cream-dim);
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .error-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .details-toggle {
            margin-top: 50px;
            color: var(--mocha);
            font-family: var(--font-accent);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
            display: inline-block;
            border-bottom: 1px solid transparent;
        }

        .details-toggle:hover {
            color: var(--gold);
            border-bottom-color: var(--gold-dim);
        }

        .details-content {
            max-height: 0;
            overflow: hidden;
            transition: all 0.6s var(--ease-smooth);
            opacity: 0;
            text-align: left;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 4px;
            margin-top: 20px;
        }

        .details-content.visible {
            max-height: 300px;
            opacity: 1;
            padding: 20px;
            border: 1px solid rgba(212, 168, 83, 0.1);
        }

        .details-content pre {
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.85rem;
            color: var(--mocha);
            white-space: pre-wrap;
            word-break: break-all;
        }

        /* Projector Flicker Effect */
        .flicker-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.015);
            pointer-events: none;
            z-index: 10;
            animation: flicker 0.1s infinite;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .error-card:hover .flicker-overlay {
            opacity: 1;
        }

        .error-code.flicker {
            position: relative;
        }

        .error-code.flicker::before,
        .error-code.flicker::after {
            content: attr(data-text);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.5;
            display: none;
        }

        .error-card:hover .error-code::before {
            display: block;
            color: #ff0000;
            z-index: -1;
            animation: glitch 0.3s cubic-bezier(.25, .46, .45, .94) both infinite;
            transform: translateX(-2px);
        }

        .error-card:hover .error-code::after {
            display: block;
            color: #00ffff;
            z-index: -2;
            animation: glitch 0.3s cubic-bezier(.25, .46, .45, .94) reverse both infinite;
            transform: translateX(2px);
        }

        @keyframes glitch {
            0% { transform: translate(0); }
            20% { transform: translate(-3px, 3px); }
            40% { transform: translate(-3px, -3px); }
            60% { transform: translate(3px, 3px); }
            80% { transform: translate(3px, -3px); }
            100% { transform: translate(0); }
        }

        @media (max-width: 480px) {
            .error-code { font-size: 5rem; }
            .error-title { font-size: 1.8rem; }
        }
    </style>
</head>
<body>
    <div class="film-grain"></div>
    <div class="page-transition active" id="pageTransition">
        <span class="trans-logo">LUMIÈRE</span>
    </div>

    <div class="error-container">
        <div class="vintage-card error-card">
            <div class="flicker-overlay"></div>
            <div class="corner-dec corner-tl"></div>
            <div class="corner-dec corner-tr"></div>
            <div class="corner-dec corner-bl"></div>
            <div class="corner-dec corner-br"></div>

            <div class="error-code flicker" data-text="<?= htmlspecialchars($error_code) ?>"><?= htmlspecialchars($error_code) ?></div>
            <h1 class="error-title"><?= htmlspecialchars($display_title) ?></h1>
            <p class="error-message"><?= htmlspecialchars($error_message) ?></p>

            <div class="error-actions">
                <button onclick="location.reload()" class="btn-primary">
                    <span style="margin-right: 8px;">↻</span> Reload Scene
                </button>
                <a href="index.php" class="btn-coral">
                    Return to Lobby
                </a>
            </div>

            <div class="details-toggle" onclick="toggleDetails()">
                Technical Breakdown
            </div>
            
            <div id="detailsBox" class="details-content">
                <pre><?= htmlspecialchars($error_details) ?></pre>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        function toggleDetails() {
            const box = document.getElementById('detailsBox');
            box.classList.toggle('visible');
            
            const btn = document.querySelector('.details-toggle');
            if (box.classList.contains('visible')) {
                btn.textContent = 'Hide Breakdown';
            } else {
                btn.textContent = 'Technical Breakdown';
            }
        }

        // Add a slight random jitter
        const codeEl = document.querySelector('.error-code');
        codeEl.addEventListener('mousemove', (e) => {
            const rx = (Math.random() - 0.5) * 10;
            const ry = (Math.random() - 0.5) * 10;
            codeEl.style.transform = `translate(${rx}px, ${ry}px)`;
        });
        
        codeEl.addEventListener('mouseleave', () => {
            codeEl.style.transform = 'translate(0, 0)';
        });
    </script>
</body>
</html>
