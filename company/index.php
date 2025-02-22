<?php
session_start();
include '../auth/config.php';

// Check if user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    header("Location: ../auth/login.php"); // Redirect to login page if not logged in or not a company
    exit;
}

// Get user data from the session
$user_id = $_SESSION['user_id'];

try {
    // 1. Fetch company data from the companies table using the user ID
    $stmt = $pdo->prepare("SELECT id, name, company_website, company_description, logo FROM companies WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$company) {
        // If company data not found, handle error (e.g., redirect to login or display an error message)
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Company data not found.'];
        header("Location: ../auth/login.php");
        exit;
    }

    // Set company id to session if not already set.
    if(!isset($_SESSION['company_id'])){
       $_SESSION['company_id'] = $company['id']; 
    }
    $company_id = $_SESSION['company_id'];

    // 2. Get recent job postings for the company
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE company_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$company['id']]);  // Use $company['id'] here
    $recent_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Get application counts for each job
    $application_counts = [];
    foreach ($recent_jobs as $job) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM job_applications WHERE job_id = ?");
        $stmt->execute([$job['id']]);
        $application_counts[$job['id']] = $stmt->fetchColumn();
    }
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
    <title>Company Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/company_dashboard.css">
</head>
<body>
<?php include '../includes/header2.php'; ?>
<div class="dashboard-container">
    <aside class="sidebar" id="sidebar">
        <div class="profile-section">
            <img src="<?= htmlspecialchars($company['logo'] ?? 'https://img.freepik.com/free-vector/technology-logo-template-with-abstract-shapes_23-2148240852.jpg?t=st=1740262020~exp=1740265620~hmac=a45bacb1f9ab02ac81046f50a1e731b38e2fdc870b99455d2bf9ea8906251911&w=740') ?>" alt="Company Logo" class="profile-pic">
            <h3><?= htmlspecialchars($company['name'] ?? " ") ?></h3>
            <p><?= htmlspecialchars($company['email'] ?? "position@company.com") ?></p>
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
                    <a href="#" class="nav-link" data-target="post_job">
                        <i class="fas fa-plus-circle"></i>
                        Post a Job
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-target="company_profile">
                        <i class="fas fa-building"></i>
                        Company Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/jobnepal/auth/logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="main-content" id="main-content">
        <div id="home-content">
            <div style="display: flex; align-items: center; justify-content: start;">
                <h1 class="section-title">Welcome Back, <?= htmlspecialchars($company['name']) ?>!</h1>
            </div>

            <!-- Recent Job Postings -->
            <section class="recent-jobs">
                <h2 class="section-title">Recent Job Postings</h2>
                <div class="job-list">
                    <?php if (empty($recent_jobs)): ?>
                        <p>No recent job postings.</p>
                    <?php else: ?>
                        <?php foreach ($recent_jobs as $job): ?>
                            <div class="job-card">
                                <div>
                                    <h3><?= htmlspecialchars($job['title']) ?></h3>
                                    <p><?= htmlspecialchars($job['location']) ?></p>
                                </div>
                                <div class="application-count">
                                    <i class="fas fa-users"></i>
                                    <span><?= $application_counts[$job['id']] ?? 0 ?> Applications</span>
                                    <a href="#" class="btn manage-applications-btn" data-job-id="<?= $job['id'] ?>">Manage Applications</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <a href="#" class="btn" data-target="post_job">Post New Job</a>
            </section>
        </div>
    </main>
</div>

<script>
    const navLinks = document.querySelectorAll('.nav-link');
    const mainContent = document.getElementById('main-content');

    navLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();

            const target = this.getAttribute('data-target');

            navLinks.forEach(link => link.classList.remove('active'));
            this.classList.add('active');

            if (target === 'home') {
                 mainContent.innerHTML = `  <div style="display:flex">
                <h1 class="section-title">Welcome Back, <?= htmlspecialchars($company['name']) ?>!</h1>
            </div>

            <!-- Recent Job Postings -->
            <section class="recent-jobs">
                <h2 class="section-title">Recent Job Postings</h2>
                <div class="job-list">
                    <?php if (empty($recent_jobs)): ?>
                        <p>No recent job postings.</p>
                    <?php else: ?>
                        <?php foreach ($recent_jobs as $job): ?>
                            <div class="job-card">
                                <div>
                                    <h3><?= htmlspecialchars($job['title']) ?></h3>
                                    <p><?= htmlspecialchars($job['location']) ?></p>
                                </div>
                                <div class="application-count">
                                    <i class="fas fa-users"></i>
                                    <span><?= $application_counts[$job['id']] ?? 0 ?> Applications</span>
                                    <a href="#" class="btn manage-applications-btn" data-job-id="<?= $job['id'] ?>">Manage Applications</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <a href="#" class="btn" data-target="post_job">Post New Job</a>
            </section>`;
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

    // Event listener for "Manage Applications" buttons (loads content dynamically)
    mainContent.addEventListener('click', function(event) {
        if (event.target.classList.contains('manage-applications-btn')) {
            event.preventDefault();
            const jobId = event.target.getAttribute('data-job-id');

            // Load the manage_applications.php page with the job ID
            fetch(`manage_applications.php?job_id=${jobId}`)
                .then(response => response.text())
                .then(data => {
                    mainContent.innerHTML = data;
                })
                .catch(error => {
                    console.error('Error loading applications:', error);
                    mainContent.innerHTML = '<p>Error loading applications.</p>';
                });
        }
    });

    // Add sidebar toggle functionality (if needed)
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('open');
    }
</script>
</body>
</html>