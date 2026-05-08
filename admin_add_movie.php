<?php
// Error displaying
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index_login.php');
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation
    if (!isset($_POST['title']) || empty($_POST['title'])) {
        $message = "Error: Movie title is required.";
    } elseif (empty($_POST['start_date'])) {
        $message = "Error: Start date is required.";
    } elseif (empty($_POST['time_slots'])) {
        $message = "Error: At least one time slot is required.";
    } else {
        $organiser_id = $_SESSION['user_id'];
        $title        = $_POST['title'];
        $year         = $_POST['year'] ?? "";
        $director     = $_POST['director'] ?? "";
        $genre        = $_POST['genre'] ?? "";
        $duration     = $_POST['duration'] ?? 0;
        $starring     = $_POST['starring'] ?? "";
        $synopsis     = $_POST['synopsis'] ?? "";
        $start_date   = $_POST['start_date'] ?? "";
        $price        = $_POST['price'] ?? 0.00;
        $auditorium   = $_POST['auditorium'] ?? 0;
        $time_slots   = $_POST['time_slots'] ?? "";

        // Handle Poster Uploading
        $poster_path = "";
        if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['poster']['tmp_name'];
            $file_ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);

            // random code to prevent file name collisions
            $safe_title = preg_replace('/[^a-z0-9]+/', '_', strtolower($title));
            $filename = "poster_" . $safe_title . "_" . time() . "." . $file_ext;
            $poster_path = "assets/images/" . $filename;

            // auto create assets/images folder if not exists
            if (!is_dir('assets/images/')) {
                mkdir('assets/images/', 0777, true);
            }
            move_uploaded_file($file_tmp, $poster_path);
        }

        // Insert Movie
        $stmt = $conn->prepare("INSERT INTO movies (movie_name, user_id, director, genre, release_year, starring, description, poster_path, duration, price, start_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Bind parameters
        if ($stmt) {
            $stmt->bind_param("sissssssids", $title, $organiser_id, $director, $genre, $year, $starring, $synopsis, $poster_path, $duration, $price, $start_date);

            // Execute and get last inserted id
            if ($stmt->execute()) {
                $movie_id = $conn->insert_id;
                $stmt->close();

                // Insert Showtimes
                $slots = explode(',', $time_slots);
                foreach ($slots as $slot) {
                    $slot = trim($slot);
                    if ($slot !== "") {
                        $stmt_st = $conn->prepare("INSERT INTO showtimes (movie_id, auditorium_number, show_date, start_time) VALUES (?, ?, ?, ?)");
                        if ($stmt_st) {
                            $stmt_st->bind_param("iiss", $movie_id, $auditorium, $start_date, $slot);
                            $stmt_st->execute();
                            $stmt_st->close();
                        }
                    }
                }
                $message = "Picture added to archive.";
            } else {
                $message = "Error executing statement: " . $stmt->error;
                $stmt->close();
            }
        } else {
            $message = "Error preparing statement: " . $conn->error;
        }
    }
}
?>

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

    <!-- Navigation Bar -->
    <nav class="lumiere-nav liquidGlass-wrapper" style="padding: 15px 5%; border-radius: 0 0 15px 15px; border-bottom: none; background: transparent;">
        <div class="liquidGlass-effect"></div>
        <div class="liquidGlass-tint"></div>
        <div class="liquidGlass-shine"></div>
        <div class="liquidGlass-content" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <a href="dashboard_admin.php" class="lumiere-logo" style="gap: 10px;">
                <img src="assets/images/logo.svg?v=5" alt="LUMIÈRE" style="height: 40px;">
                <span style="font-family: var(--font-accent); font-size: 1rem; color: var(--mocha); letter-spacing: 0.2em;">STAFF</span>
            </a>
            <div class="nav-links">
                <a href="dashboard_admin.php" class="nav-link">Dashboard</a>
                <a href="admin_add_movie.php" class="nav-link" style="color: var(--sunset-coral);">Catalog</a>
                <a href="admin_set_promotion.php" class="nav-link">Promotions</a>
                <a href="movies.php" class="nav-link" style="color: var(--sunset-rose);">Exit Staff</a>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
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
                <form action="#" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label style="color: var(--bg-deep); font-weight: 600;">Picture Title</label>
                        <input type="text" name="title" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="Enter Official Title" required>
                    </div>

                    <div style="display: flex; gap: 30px;">
                        <div class="form-group" style="flex: 1;">
                            <label style="color: var(--bg-deep); font-weight: 600;">Release Year</label>
                            <input type="number" name="year" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="e.g. 2024">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label style="color: var(--bg-deep); font-weight: 600;">Director</label>
                            <input type="text" name="director" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="Name of Director">
                        </div>
                    </div>

                    <div style="display: flex; gap: 30px;">
                        <div class="form-group" style="flex: 1;">
                            <label style="color: var(--bg-deep); font-weight: 600;">Genre</label>
                            <input type="text" name="genre" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="Drama, Sci-Fi, etc.">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label style="color: var(--bg-deep); font-weight: 600;">Duration (min)</label>
                            <input type="number" name="duration" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="e.g. 120">
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="color: var(--bg-deep); font-weight: 600;">Starring</label>
                        <input type="text" name="starring" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="Lead Actors">
                    </div>

                    <div class="form-group">
                        <label style="color: var(--bg-deep); font-weight: 600;">Synopsis</label>
                        <textarea name="synopsis" class="typewriter-input" style="color: var(--bg-deep); height: 100px; resize: none; background: transparent; border-color: var(--mocha);" placeholder="Summary of the picture..."></textarea>
                    </div>

                    <div style="display: flex; gap: 30px;">
                        <div class="form-group" style="flex: 1;">
                            <label style="color: var(--bg-deep); font-weight: 600;">Start Date</label>
                            <input type="date" name="start_date" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" required>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label style="color: var(--bg-deep); font-weight: 600;">Base Price (�RM)</label>
                            <input type="number" name="price" step="0.01" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="12.00">
                        </div>
                    </div>

                    <div style="display: flex; gap: 30px;">
                        <div class="form-group" style="flex: 1;">
                            <label style="color: var(--bg-deep); font-weight: 600;">Auditorium No.</label>
                            <input type="number" name="auditorium" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="1-8">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label style="color: var(--bg-deep); font-weight: 600;">Time Slots</label>
                            <input type="text" name="time_slots" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha);" placeholder="14:00, 17:30, 20:00" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="color: var(--bg-deep); font-weight: 600;">Upload Celluloid Negative (Poster Image)</label>
                        <input type="file" name="poster" class="typewriter-input" style="color: var(--bg-deep); background: transparent; border-color: var(--mocha); padding: 8px;" accept="image/*" required onchange="checkFileSize(this)">
                    </div>

                    <button class="btn-primary" style="width: 100%; margin-top: 20px; color: var(--bg-deep); border-color: var(--bg-deep);" type="submit">
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

    <?php if ($message !== ""): ?>
    <script>
        Swal.fire({
            title: '<?php echo strpos($message, 'Error') === false ? 'Approved' : 'Error'; ?>',
            text: '<?php echo $message; ?>',
            icon: '<?php echo strpos($message, 'Error') === false ? 'success' : 'error'; ?>',
            background: '#F2E8D5',
            color: '#0D0B0E',
            iconColor: '<?php echo strpos($message, 'Error') === false ? '#2A7A7A' : '#B22222'; ?>',
            confirmButtonColor: '<?php echo strpos($message, 'Error') === false ? '#2A7A7A' : '#B22222'; ?>'
        }).then(() => {
            <?php if (strpos($message, 'Error') === false): ?>
            triggerPageTransition('dashboard_admin.php');
            <?php endif; ?>
        });
    </script>
    <?php endif; ?>

    <script>
        function checkFileSize(input) {
            if (input.files && input.files[0]) {
                const fileSize = input.files[0].size / 1024 / 1024; // MB
                if (fileSize > 2) {
                    Swal.fire({
                        title: 'Celluloid Too Large',
                        text: 'The negative (poster) must be under 2MB for the archive to process it. Please compress the file.',
                        icon: 'warning',
                        background: '#F2E8D5',
                        color: '#0D0B0E',
                        confirmButtonColor: '#2A7A7A'
                    });
                    input.value = ''; // Clear the selection
                }
            }
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
