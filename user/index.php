<?php
// dashboard.php
session_start();
include '../auth/config.php';

// Check if user is logged in and is a job seeker
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'job_seeker') {
    header("Location: ../auth/login.php");
    exit;
}

// Get user data from the session
$user_id = $_SESSION['user_id'];

try {
    // 1. Fetch user data from the users table based on the user ID
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE id = ? AND role = 'job_seeker'");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // If user not found in the users table or user_type is not 'job_seeker', handle error (e.g., redirect to login)
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'User not found or invalid user type.'];
        header("Location: ../auth/login.php");
        exit;
    }

    // 2. Fetch job seeker data from the job_seekers table using the user ID
    $stmt = $pdo->prepare("SELECT name, profile_pic FROM job_seekers WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $job_seeker = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$job_seeker) {
        // If job seeker data not found, you might want to log this as an error
        // and display a default message or redirect the user to an edit profile page
        $job_seeker = ['name' => 'N/A', 'profile_pic' => null]; // Set default values
    }

    // 3. Fetch job applications data from the job_applications table
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM job_applications WHERE job_seeker_id = ?");
    $stmt->execute([$user_id]);
    $applications_sent = $stmt->fetchColumn();



    // 5. Fetch Recent Applications
    $stmt = $pdo->prepare("
        SELECT
            ja.id AS application_id,
            ja.status,
            ja.applied_at,
            j.title AS job_title,
            c.name AS company_name
        FROM job_applications ja
        JOIN jobs j ON ja.job_id = j.id
        JOIN companies c ON j.company_id = c.id
        WHERE ja.job_seeker_id = ?
        ORDER BY ja.applied_at DESC
        LIMIT 5
    ");
    $stmt->execute([$user_id]);
    $recent_applications = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $profile_strength = 75;
} catch (PDOException $e) {
    // Handle database errors
    error_log("Database error: " . $e->getMessage());
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'A database error occurred. Please try again later.'];
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Seeker Dashboard</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/top.css">
    <link rel="stylesheet" href="../assets/css/popular.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>

    </style>
</head>

<body>
    <?php include '../includes/header2.php'; ?>

    <div class="dashboard-container">
        <button class="toggle-button" onclick="toggleSidebar()">☰</button>

        <aside class="sidebar" id="sidebar">
            <div class="profile-section">
                <img src="<?= htmlspecialchars($job_seeker['profile_pic'] ?? 'https://img.icons8.com/?size=100&id=PBofAcohuuFl&format=png&color=000000') ?>"
                    alt="Profile Picture" class="profile-pic">
                <h3><?= htmlspecialchars($job_seeker['name'] ?? 'N/A') ?></h3>
                <p><?= htmlspecialchars($user['email']) ?></p>
            </div>

            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link active" data-target="home">
                            <i class="fas fa-home"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-target="profile">
                            <i class="fas fa-user"></i>
                            My Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-target="applications">
                            <i class="fas fa-file-alt"></i>
                            Applications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-target="settings">
                            <i class="fas fa-cog"></i>
                            Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/jobnepal/auth/logout.php" class="nav-link" data-target="logout">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="main-content" id="main-content">
            <!-- Initial content (Dashboard Home) -->
            <div id="home-content">
                <div style="display:flex">
                    <h1 class="section-title">Welcome Back, <?= htmlspecialchars($job_seeker['name'] ?? 'N/A') ?>!</h1>
                </div>

                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Applications Sent</h3>
                        <p class="stat-number"><?= htmlspecialchars($applications_sent) ?></p>
                    </div>

                    <div class="stat-card">
                        <h3>Profile Strength</h3>
                        <div class="progress-bar">
                            <div class="progress" style="width: <?= $profile_strength ?>%"><?= $profile_strength ?>%
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Recent Applications -->
                <section class="recent-applications">
                    <h2 class="section-title">Recent Applications</h2>
                    <div class="job-list">
                        <?php foreach ($recent_applications as $application): ?>
                            <div class="job-card">
                                <div>
                                    <h3><?= htmlspecialchars($application['job_title']) ?></h3>
                                    <p><?= htmlspecialchars($application['company_name']) ?></p>
                                    <p>Status: <?= htmlspecialchars($application['status']) ?></p>
                                </div>
                                <span class="application-date"><?= htmlspecialchars(date('Y-m-d', strtotime($application['applied_at']))) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }

        const navLinks = document.querySelectorAll('.nav-link');
        const mainContent = document.getElementById('main-content');

        navLinks.forEach(link => {
            link.addEventListener('click', function (event) {
                event.preventDefault(); // Prevent default link behavior

                const target = this.getAttribute('data-target');

                // Remove 'active' class from all links
                navLinks.forEach(link => link.classList.remove('active'));

                // Add 'active' class to the clicked link
                this.classList.add('active');

                // Load content based on the target
                if (target === 'home') {
                    mainContent.innerHTML = `
                  <div style="display:flex">
                        <h1 class="section-title">Welcome Back, <?= htmlspecialchars($job_seeker['name'] ?? 'N/A') ?>!</h1>
                    </div>

                    <!-- Stats Grid -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3>Applications Sent</h3>
                            <p class="stat-number"><?= htmlspecialchars($applications_sent) ?></p>
                        </div>

                        <div class="stat-card">
                            <h3>Profile Strength</h3>
                            <div class="progress-bar">
                                <div class="progress" style="width: <?= $profile_strength ?>%"><?= $profile_strength ?>%
                                </div>
                            </div>
                        </div>
                    </div>

  

                    <!-- Recent Applications -->
                    <section class="recent-applications">
                        <h2 class="section-title">Recent Applications</h2>
                        <div class="job-list">
                            <?php foreach ($recent_applications as $application): ?>
                                <div class="job-card">
                                    <div>
                                        <h3><?= htmlspecialchars($application['job_title']) ?></h3>
                                        <p><?= htmlspecialchars($application['company_name']) ?></p>
                                        <p>Status: <?= htmlspecialchars($application['status']) ?></p>
                                    </div>
                                    <span class="application-date"><?= htmlspecialchars(date('Y-m-d', strtotime($application['applied_at']))) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
            `;
                }
                 else if (target == "logout") {
                    fetch('/jobnepal/auth/logout.php')
                        .then(() => {
                            window.location.href = '/jobnepal/auth/login.php'; 
                        })
                        .catch(error => console.error('Logout failed:', error));
                }
                else {
                    fetch(target + '.php')
                        .then(response => response.text())
                        .then(data => {
                            mainContent.innerHTML = data;
                        })
                        .catch(error => {
                            console.error('Error loading content:', error);
                            mainContent.innerHTML = '<p>Error loading content.</p>';
                        });
                }
            });
        });
    </script>
</body>

</html>