<?php
// dashboard.php
session_start();
include '../auth/config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /jobnepal/admin/login.php");
    exit;
}

// Get user data from the session
$user_id = $_SESSION['user_id'];

try {
    // 1. Fetch user data from the users table based on the user ID
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE id = :user_id AND role = 'admin'");  // Fetch admin data
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Handle the case where the user is not found or not an admin
        session_destroy();
        header("Location: ../auth/login.php");
        exit;
    }

    // 2. Fetch admin-related data (e.g., total users, total companies, recent activity)
    // Get total number of users:
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $totalUsers = $stmt->fetchColumn();

    // Get total number of companies:
    $stmt = $pdo->query("SELECT COUNT(*) FROM companies");
    $totalCompanies = $stmt->fetchColumn();

    // Get total number of jobs:
    $stmt = $pdo->query("SELECT COUNT(*) FROM jobs");
    $totalJobs = $stmt->fetchColumn();

    // Get total job applications
    $stmt = $pdo->query("SELECT COUNT(*) FROM job_applications");
    $totalApplications = $stmt->fetchColumn();

    // Fetch recent user registrations (last 5) - Include Job Seeker Name
    $stmt = $pdo->prepare("SELECT u.id, u.email, u.created_at, js.name AS job_seeker_name
                           FROM users u
                           LEFT JOIN job_seekers js ON u.id = js.user_id
                           WHERE u.role = 'job_seeker'
                           ORDER BY u.created_at DESC LIMIT 5");
    $stmt->execute();
    $recentUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch recent company registrations (last 5) - Include Company Website
    $stmt = $pdo->prepare("SELECT c.id, c.name, c.created_at, c.company_website
                           FROM companies c
                           ORDER BY c.created_at DESC LIMIT 5");
    $stmt->execute();
    $recentCompanies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch recent job postings (last 5) - Include Company Name
    $stmt = $pdo->prepare("SELECT j.id, j.title, j.created_at, c.name AS company_name
                           FROM jobs j
                           JOIN companies c ON j.company_id = c.id
                           ORDER BY j.created_at DESC LIMIT 5");
    $stmt->execute();
    $recentJobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

       
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .main-content .section-title {
            color: #343a40;
            margin-bottom: 20px;
        }

        .main-content .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .main-content .stat-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .main-content .stat-card h3 {
            color: #007bff;
            margin-bottom: 10px;
        }

        .main-content .stat-card p.stat-number {
            font-size: 2rem;
            color: #495057;
        }

        .main-content .recent-activity {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .main-content .recent-activity h2 {
            color: #343a40;
            margin-bottom: 20px;
        }

        .main-content .recent-activity ul {
            list-style: none;
            padding: 0;
        }

        .main-content .recent-activity li {
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .main-content .recent-activity li span {
            font-style: italic;
            color: #777;
        }


        .toggle-button {
            display: none;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .date{
            margin-right: 20px;
            font-weight: 900;
        }
    </style>

</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <button class="toggle-button" onclick="toggleSidebar()">☰</button>

        <aside class="sidebar" id="sidebar">
            <div class="profile-section">
                <img src="https://img.icons8.com/?size=100&id=PBofAcohuuFl&format=png&color=000000"
                    alt="Admin Profile Picture" class="profile-pic">
                <h6><?= htmlspecialchars($user['email'] ?? 'Admin') ?></h6>
                <p>Administrator</p>
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
                        <a href="#" class="nav-link" data-target="manage-users">
                            <i class="fas fa-users"></i>
                            Manage Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-target="manage-companies">
                            <i class="fas fa-building"></i>
                            Manage Companies
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-target="manage-jobs">
                            <i class="fas fa-briefcase"></i>
                            Manage Jobs
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
                    <h1 class="section-title">Welcome, Administrator!</h1>
                </div>

                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Users</h3>
                        <p class="stat-number"><?= htmlspecialchars($totalUsers) ?></p>
                    </div>

                    <div class="stat-card">
                        <h3>Total Companies</h3>
                        <p class="stat-number"><?= htmlspecialchars($totalCompanies) ?></p>
                    </div>

                    <div class="stat-card">
                        <h3>Total Jobs</h3>
                        <p class="stat-number"><?= htmlspecialchars($totalJobs) ?></p>
                    </div>

                    <div class="stat-card">
                        <h3>Total Applications</h3>
                        <p class="stat-number"><?= htmlspecialchars($totalApplications) ?></p>
                    </div>
                </div>



                <!-- Recent Activity -->
                <section class="recent-activity">
                    <h2 class="section-title">Recent User Registrations</h2>
                    <ul class="list-group">
                        <?php foreach ($recentUsers as $user): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold"><?= htmlspecialchars($user['email']) ?></div>
                                    <?php if ($user['job_seeker_name']): ?>
                                        Job Seeker: <?= htmlspecialchars($user['job_seeker_name']) ?>
                                    <?php else: ?>
                                        Job Seeker Name Unavailable
                                    <?php endif; ?>
                                </div>
                                <span
                                    class="date"><?= htmlspecialchars(date('Y-m-d', strtotime($user['created_at']))) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>


                    <h2 class="section-title">Recent Company Registrations</h2>

                    <ul class="list-group">
                        <?php foreach ($recentCompanies as $company): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold"><?= htmlspecialchars($company['name']) ?></div>
                                    <a href="<?= htmlspecialchars($company['company_website']) ?>" target="_blank"
                                        rel="noopener noreferrer">Website</a>
                                </div>
                                <span
                                    class="date"><?= htmlspecialchars(date('Y-m-d', strtotime($company['created_at']))) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>


                    <h2 class="section-title">Recent Job Postings</h2>
                    <ul class="list-group">
                        <?php foreach ($recentJobs as $job): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold"><?= htmlspecialchars($job['title']) ?></div>
                                    Company: <?= htmlspecialchars($job['company_name']) ?>
                                </div>
                                <span
                                    class="date"><?= htmlspecialchars(date('Y-m-d', strtotime($job['created_at']))) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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
                    mainContent.innerHTML = ` <div style="display:flex">
                        <h1 class="section-title">Welcome, Administrator!</h1>
                    </div>

                    <!-- Stats Grid -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3>Total Users</h3>
                            <p class="stat-number"><?= htmlspecialchars($totalUsers) ?></p>
                        </div>

                        <div class="stat-card">
                            <h3>Total Companies</h3>
                            <p class="stat-number"><?= htmlspecialchars($totalCompanies) ?></p>
                        </div>

                         <div class="stat-card">
                            <h3>Total Jobs</h3>
                            <p class="stat-number"><?= htmlspecialchars($totalJobs) ?></p>
                        </div>

                        <div class="stat-card">
                            <h3>Total Applications</h3>
                            <p class="stat-number"><?= htmlspecialchars($totalApplications) ?></p>
                        </div>
                    </div>

  

                    <!-- Recent Activity -->
                 <section class="recent-activity">
                    <h2 class="section-title">Recent User Registrations</h2>
                     <ul class="list-group">
                        <?php foreach ($recentUsers as $user): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold"><?= htmlspecialchars($user['email']) ?></div>
                                    <?php if ($user['job_seeker_name']): ?>
                                        Job Seeker: <?= htmlspecialchars($user['job_seeker_name']) ?>
                                    <?php else: ?>
                                        Job Seeker Name Unavailable
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?= htmlspecialchars(date('Y-m-d', strtotime($user['created_at']))) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>


                    <h2 class="section-title">Recent Company Registrations</h2>
                    
                    <ul class="list-group">
                        <?php foreach ($recentCompanies as $company): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold"><?= htmlspecialchars($company['name']) ?></div>
                                    <a href="<?= htmlspecialchars($company['company_website']) ?>" target="_blank" rel="noopener noreferrer">Website</a>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?= htmlspecialchars(date('Y-m-d', strtotime($company['created_at']))) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>


                     <h2 class="section-title">Recent Job Postings</h2>
                       <ul class="list-group">
                        <?php foreach ($recentJobs as $job): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold"><?= htmlspecialchars($job['title']) ?></div>
                                    Company: <?= htmlspecialchars($job['company_name']) ?>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?= htmlspecialchars(date('Y-m-d', strtotime($job['created_at']))) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            
                    
            `;
                }
                else if (target === "manage-users") {
                    fetch('manage_users.php')
                        .then(response => response.text())
                        .then(data => {
                            mainContent.innerHTML = data;
                        })
                        .catch(error => {
                            console.error('Error loading content:', error);
                            mainContent.innerHTML = '<p>Error loading content.</p>';
                        });
                } else if (target === "manage-companies") {
                    fetch('manage_companies.php')
                        .then(response => response.text())
                        .then(data => {
                            mainContent.innerHTML = data;
                        })
                        .catch(error => {
                            console.error('Error loading content:', error);
                            mainContent.innerHTML = '<p>Error loading content.</p>';
                        });
                } else if (target === "manage-jobs") {
                    fetch('manage_jobs.php')
                        .then(response => response.text())
                        .then(data => {
                            mainContent.innerHTML = data;
                        })
                        .catch(error => {
                            console.error('Error loading content:', error);
                            mainContent.innerHTML = '<p>Error loading content.</p>';
                        });
                } else if (target == "logout") {
                    fetch('/jobnepal/auth/logout.php')
                        .then(() => {
                            window.location.href = '/jobnepal/';
                        })
                        .catch(error => console.error('Logout failed:', error));
                } else {
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