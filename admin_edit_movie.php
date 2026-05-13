<?php
require_once 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index_login.php');
    exit();
}

$message = "";
$movie_id = $_GET['id'] ?? null;

if (!$movie_id) {
    header('Location: dashboard_admin.php');
    exit();
}

// Fetch Current Movie Data
$movie = null;
$stmt_fetch = $conn->prepare("SELECT * FROM movies WHERE movie_id = ?");
$stmt_fetch->bind_param("i", $movie_id);
$stmt_fetch->execute();
$res = $stmt_fetch->get_result();
$movie = $res->fetch_assoc();
$stmt_fetch->close();

if (!$movie) {
    header('Location: dashboard_admin.php');
    exit();
}

// Fetch Current Showtimes (Slots)
$showtimes_res = $conn->query("SELECT * FROM showtimes WHERE movie_id = $movie_id ORDER BY start_time");
$slots = [];
$current_auditorium = "";
while ($st = $showtimes_res->fetch_assoc()) {
    $slots[] = substr($st['start_time'], 0, 5);
    $current_auditorium = $st['auditorium_number'];
}
$slot1 = $slots[0] ?? "";
$slot2 = $slots[1] ?? "";
$slot3 = $slots[2] ?? "";

// Update Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title      = $_POST['title'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $director   = $_POST['director'] ?? '';
    $genre      = $_POST['genre'] ?? '';
    $year       = $_POST['year'] ?? '';
    $starring   = $_POST['starring'] ?? '';
    $synopsis   = $_POST['synopsis'] ?? '';
    $duration   = (int)($_POST['duration'] ?? 0);
    $auditorium = $_POST['auditorium'] ?? $current_auditorium;
    
    $s1 = $_POST['slot1'] ?? "";
    $s2 = $_POST['slot2'] ?? "";
    $s3 = $_POST['slot3'] ?? "";

    if (empty($title)) {
        $message = "Error: Movie title is required.";
    } elseif (empty($start_date)) {
        $message = "Error: Start date is required.";
    } elseif (empty($s1) || empty($s2) || empty($s3)) {
        $message = "Error: All three showtime slots are required.";
    } else {
        $t1 = strtotime($s1);
        $t2 = strtotime($s2);
        $t3 = strtotime($s3);
        
        $sorted_times = [$t1, $t2, $t3];
        sort($sorted_times);

        if (($sorted_times[1] - $sorted_times[0]) < ($duration * 60) || 
            ($sorted_times[2] - $sorted_times[1]) < ($duration * 60)) {
            $message = "Error: Showtimes must be at least $duration minutes apart.";
        } else {
            // Poster Upload 
            $poster_path = $movie['poster_path'];
            if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['poster']['tmp_name'];
                $file_ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
                $safe_title = preg_replace('/[^a-z0-9]+/', '_', strtolower($title));
                $filename = "poster_" . $safe_title . "_" . time() . "." . $file_ext;
                $new_path = "assets/images/" . $filename;
                
                if (move_uploaded_file($file_tmp, $new_path)) {
                    $poster_path = $new_path;
                }
            }

            // 4. Update Movie Table
            $stmt_up = $conn->prepare("UPDATE movies SET movie_name=?, director=?, genre=?, release_year=?, starring=?, description=?, poster_path=?, duration=?, start_date=? WHERE movie_id=?"); 
            $stmt_up->bind_param("sssssssisi", $title, $director, $genre, $year, $starring, $synopsis, $poster_path, $duration, $start_date, $movie_id);

            if ($stmt_up->execute()) {
                // 5. Sync Showtimes (Update existing or Insert new)
                $stmt_get_ids = $conn->prepare("SELECT showtime_id FROM showtimes WHERE movie_id = ? ORDER BY showtime_id");
                $stmt_get_ids->bind_param("i", $movie_id);
                $stmt_get_ids->execute();
                $existing_res = $stmt_get_ids->get_result();
                
                $existing_ids = [];
                while($r = $existing_res->fetch_assoc()) $existing_ids[] = $r['showtime_id'];

                $new_slots = [$s1, $s2, $s3];
                foreach($new_slots as $idx => $ts) {
                    if(isset($existing_ids[$idx])) {
                        // Update existing slot
                        $sid = $existing_ids[$idx];
                        $up = $conn->prepare("UPDATE showtimes SET auditorium_number=?, start_time=? WHERE showtime_id=?");
                        $up->bind_param("isi", $auditorium, $ts, $sid);
                        $up->execute();
                    } else {
                        $ins = $conn->prepare("INSERT INTO showtimes (movie_id, auditorium_number, start_time) VALUES (?, ?, ?)");
                        $ins->bind_param("iis", $movie_id, $auditorium, $ts);
                        $ins->execute();
                    }
                }

                // Cleanup extra slots
                if(count($existing_ids) > 3) {
                    for($i=3; $i<count($existing_ids); $i++) {
                        $sid = $existing_ids[$i];
                        $del = $conn->prepare("DELETE FROM showtimes WHERE showtime_id = ?");
                        $del->bind_param("i", $sid);
                        $del->execute();
                    }
                }
                $message = "Movie details and showtime slots successfully updated.";
                
                // Update local array for immediate UI feedback
                $movie['movie_name'] = $title;
                $movie['poster_path'] = $poster_path;
            } else {
                $message = "Database Error: " . $stmt_up->error;
            }
            $stmt_up->close();
        }
    }
}

// Delete
if (isset($_POST['delete_movie']) && $_POST['delete_movie'] === '1') {
    // Fetch poster path
    $stmt_path = $conn->prepare("SELECT poster_path FROM movies WHERE movie_id = ? AND user_id = ?");
    $admin_id = $_SESSION['user_id'];
    $stmt_path->bind_param("ii", $movie_id, $admin_id);
    $stmt_path->execute();
    $path_res = $stmt_path->get_result();
    $path_data = $path_res->fetch_assoc();
    $stmt_path->close();

    if ($path_data && !empty($path_data['poster_path'])) {
        $p_path = $path_data['poster_path'];
        if (file_exists($p_path) && strpos($p_path, 'http') === false) {
            unlink($p_path);
        }
    }

    $stmt_del = $conn->prepare("DELETE FROM movies WHERE movie_id = ? AND user_id = ?");
    $stmt_del->bind_param("ii", $movie_id, $admin_id);
    
    if ($stmt_del->execute()) {
        header('Location: dashboard_admin.php?msg=Movie+and+assets+successfully+purged');
        exit();
    } else {
        $message = "Error during deletion: " . $stmt_del->error;
    }
    $stmt_del->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUMIÈRE - Edit Celluloid</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/pages/dashboard.css">
    <link rel="stylesheet" href="css/pages/footer.css">
    <link rel="stylesheet" href="css/global.css">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
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
                <img src="assets/images/logo.svg" alt="LUMIÈRE" style="height: 40px;">
                <span style="font-family: var(--font-accent); font-size: 1rem; color: var(--mocha); letter-spacing: 0.2em;">STAFF</span>
            </a>
            <div class="nav-links">
                <a href="dashboard_admin.php" class="nav-link">Dashboard</a>
                <a href="admin_add_movie.php" class="nav-link">Catalog</a>
                <a href="admin_set_promotion.php" class="nav-link">Promotions</a>
                <a href="movies.php" class="nav-link" style="color: var(--sunset-rose);">Exit Staff</a>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="page-wrapper org-wrapper" style="padding: 120px 5% 60px;">
        <h1 class="fade-up" style="margin-bottom: 30px;">Edit: <?php echo htmlspecialchars($movie['movie_name']); ?></h1>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 40px; max-width: 1200px; margin: 0 auto;">
                
                <!-- Left Column: Primary Details -->
                <div class="fade-up">
                    <div class="liquidGlass-wrapper" style="border-radius: 12px; background: transparent;">
                        <div class="liquidGlass-effect"></div>
                        <div class="liquidGlass-tint" style="background: rgba(255, 255, 255, 0.75);"></div>
                        <div class="liquidGlass-content" style="padding: 40px;">
                            <div class="form-group">
                                <label style="color: var(--bg-deep); font-weight: 600;">Picture Title</label>
                                <input type="text" name="title" class="typewriter-input" style="color: var(--bg-deep); border-color: var(--mocha);" value="<?php echo htmlspecialchars($movie['movie_name']); ?>" required>
                            </div>

                            <div style="display: flex; gap: 20px;">
                                <div class="form-group" style="flex: 1;">
                                    <label style="color: var(--bg-deep); font-weight: 600;">Director</label>
                                    <input type="text" name="director" class="typewriter-input" style="color: var(--bg-deep); border-color: var(--mocha);" value="<?php echo htmlspecialchars($movie['director']); ?>">
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label style="color: var(--bg-deep); font-weight: 600;">Release Year</label>
                                    <input type="number" name="year" class="typewriter-input" style="color: var(--bg-deep); border-color: var(--mocha);" value="<?php echo $movie['release_year']; ?>">
                                </div>
                            </div>

                            <div style="display: flex; gap: 20px;">
                                <div class="admin-form-group" style="flex: 1;">
                                    <label style="color: var(--bg-deep); font-weight: 600;">Genre</label>
                                    <select name="genre" class="typewriter-input" style="color: var(--bg-deep); border-color: var(--mocha); width: 100%; cursor: pointer;">
                                        <?php
                                        $genres = ['Action', 'Adventure', 'Comedy', 'Crime', 'Drama', 'Fantasy', 'Historical', 'Horror', 'Musical', 'Romance', 'Sci-Fi', 'Thriller'];
                                        foreach ($genres as $g) {
                                            $selected = ($movie['genre'] === $g) ? 'selected' : '';
                                            echo "<option value=\"$g\" $selected>$g</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label style="color: var(--bg-deep); font-weight: 600;">Runtime (min)</label>
                                    <input type="number" name="duration" class="typewriter-input" style="color: var(--bg-deep); border-color: var(--mocha);" value="<?php echo $movie['duration']; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label style="color: var(--bg-deep); font-weight: 600;">Starring</label>
                                <input type="text" name="starring" class="typewriter-input" style="color: var(--bg-deep); border-color: var(--mocha);" value="<?php echo htmlspecialchars($movie['starring']); ?>">
                            </div>

                            <div class="form-group">
                                <label style="color: var(--bg-deep); font-weight: 600;">Synopsis</label>
                                <textarea name="synopsis" class="typewriter-input" style="color: var(--bg-deep); border-color: var(--mocha); height: 100px; resize: none;"><?php echo htmlspecialchars($movie['description']); ?></textarea>
                            </div>

                            <div style="display: flex; gap: 20px;">
                                <div class="form-group" style="flex: 1;">
                                    <label style="color: var(--bg-deep); font-weight: 600;">Start Date</label>
                                    <input type="date" name="start_date" class="typewriter-input" style="color: var(--bg-deep); border-color: var(--mocha);" value="<?php echo $movie['start_date']; ?>" required>
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label style="color: var(--bg-deep); font-weight: 600;">Auditorium No.</label>
                                    <input type="number" name="auditorium" class="typewriter-input" style="color: var(--bg-deep); border-color: var(--mocha);" value="<?php echo $current_auditorium; ?>">
                                </div>
                            </div>

                            <p style="color: var(--mocha); font-family: var(--font-accent); font-size: 0.75rem; text-transform: uppercase; margin-bottom: 10px; margin-top: 20px;">Showtime Slots (Must be <?= $movie['duration'] ?>m apart)</p>
                            <div style="display: flex; gap: 15px;">
                                <div class="form-group" style="flex: 1;">
                                    <label style="font-size: 0.7rem; color: var(--bg-deep);">Slot 1</label>
                                    <input type="time" name="slot1" class="typewriter-input" style="color: var(--bg-deep); border-color: var(--mocha);" value="<?php echo $slot1; ?>" required>
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label style="font-size: 0.7rem; color: var(--bg-deep);">Slot 2</label>
                                    <input type="time" name="slot2" class="typewriter-input" style="color: var(--bg-deep); border-color: var(--mocha);" value="<?php echo $slot2; ?>" required>
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label style="font-size: 0.7rem; color: var(--bg-deep);">Slot 3</label>
                                    <input type="time" name="slot3" class="typewriter-input" style="color: var(--bg-deep); border-color: var(--mocha);" value="<?php echo $slot3; ?>" required>
                                </div>
                            </div>

                            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 20px; color: var(--bg-deep); border-color: var(--bg-deep);">
                                Save Amendments
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Visuals -->
                <div class="fade-right">
                    <div style="background: rgba(255,255,255,0.1); padding: 30px; border: 1px solid rgba(212, 168, 83, 0.1); border-radius: 12px; backdrop-filter: blur(10px);">
                        <h3 style="color: var(--gold); margin-bottom: 20px; font-family: var(--font-accent);">Current Celluloid Poster</h3>
                        <img src="<?php echo htmlspecialchars($movie['poster_path']); ?>" alt="Current Poster" style="width: 100%; border-radius: 8px; border: 1px solid rgba(212, 168, 83, 0.2); margin-bottom: 20px;">
                        
                        <div class="form-group">
                            <label style="color: var(--mocha); font-weight: 600;">Update Poster (Optional)</label>
                            <input type="file" name="poster" class="typewriter-input" style="color: var(--mocha); border-color: var(--mocha); padding: 8px;" accept="image/*" onchange="checkFileSize(this)">
                        </div>

                        <div style="margin-top: 40px; border-top: 1px solid rgba(212, 168, 83, 0.1); padding-top: 30px;">
                            <button type="button" class="btn-primary" onclick="confirmDelete()" style="width: 100%; border-color: var(--retro-red); color: var(--retro-red); background: transparent;">
                                Permanent Deletion
                            </button>
                            <input type="hidden" id="deleteTrigger" value="0">
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/main.js"></script>
    
    <?php if ($message !== ""): ?>
    <script>
        Swal.fire({
            title: '<?php echo strpos($message, 'Error') === false ? 'Approved' : 'Error'; ?>',
            text: '<?php echo $message; ?>',
            icon: '<?php echo strpos($message, 'Error') === false ? 'success' : 'error'; ?>',
            background: '#F2E8D5',
            color: '#0D0B0E',
            confirmButtonColor: '#2A7A7A'
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
                const fileSize = input.files[0].size / 1024 / 1024;
                if (fileSize > 2) {
                    Swal.fire({
                        title: 'Celluloid Too Large',
                        text: 'The negative (poster) must be under 2MB. Please compress the file.',
                        icon: 'warning',
                        background: '#F2E8D5',
                        color: '#0D0B0E',
                        confirmButtonColor: '#2A7A7A'
                    });
                    input.value = '';
                }
            }
        }

        function confirmDelete() {
            Swal.fire({
                title: 'Are you certain?',
                text: "This will permanently strike this celluloid from the archive, including all ticket sales and showtimes. This cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#b22222',
                cancelButtonColor: '#8b7355',
                confirmButtonText: 'Yes, Burn the Negative',
                cancelButtonText: 'Keep in Archive',
                background: '#F2E8D5',
                color: '#0D0B0E'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.querySelector('form');
                    const trigger = document.getElementById('deleteTrigger');
                    trigger.name = 'delete_movie';
                    trigger.value = '1';
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>
