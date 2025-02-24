<?php
// applications.php
session_start();
include '../auth/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'job_seeker') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch all applications for the job seeker with job and company details
    $stmt = $pdo->prepare("
        SELECT 
            ja.id as application_id,
            ja.status,
            ja.applied_at,
            ja.cover_letter,
            j.title as job_title,
            j.location,
            j.salary,
            j.job_type,
            c.name as company_name,
            c.logo as company_logo
        FROM job_applications ja
        JOIN jobs j ON ja.job_id = j.id
        JOIN companies c ON j.company_id = c.id
        JOIN job_seekers js ON ja.job_seeker_id = js.id
        WHERE js.user_id = ?
        ORDER BY ja.applied_at DESC
    ");
    $stmt->execute([$user_id]);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $applications = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Job Applications</title>
    <style>
        
/* Applications Page */
.applications-container {
    padding: 2rem;
}

.applications-grid {
    display: grid;
    gap: 2rem;
}

.application-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: 0 2px 4px var(--shadow-color);
    transition: transform 0.3s ease;
}

.application-card:hover {
    transform: translateY(-5px);
}

.company-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.company-logo {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

.job-details {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.job-details span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: #666;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.status-badge.pending { background: #fff3cd; color: #856404; }
.status-badge.accepted { background: #d4edda; color: #155724; }
.status-badge.rejected { background: #f8d7da; color: #721c24; }

.application-date {
    font-size: 0.8rem;
    color: #777;
    margin-left: auto;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: var(--border-radius);
    box-shadow: 0 2px 4px var(--shadow-color);
}

.empty-state i {
    color: var(--primary-color);
    margin-bottom: 1rem;
}

/* Profile Page */
.profile-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.profile-form {
    background: white;
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: 0 2px 4px var(--shadow-color);
}

.profile-pic-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.current-profile-pic {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--primary-color);
    box-shadow: 0 2px 4px var(--shadow-color);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--text-color);
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: var(--border-radius);
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
}

.form-group input[disabled] {
    background-color: #f5f5f5;
    cursor: not-allowed;
}
    </style>
</head>
<body>

<div class="applications-container">
    <h2 class="section-title">My Applications</h2>
    
    <?php if (empty($applications)): ?>
        <div class="empty-state">
            <i class="fas fa-file-alt fa-3x"></i>
            <p>You haven't applied to any jobs yet.</p>
            <a href="../jobs/search.php" class="btn">Browse Jobs</a>
        </div>
    <?php else: ?>
        <div class="applications-grid">
            <?php foreach ($applications as $app): ?>
                <div class="application-card">
                    <div class="company-info">
                        <img src="<?= htmlspecialchars($app['company_logo'] ?? '/assets/images/default-company.png') ?>" 
                             alt="<?= htmlspecialchars($app['company_name']) ?>" class="company-logo">
                        <div>
                            <h3><?= htmlspecialchars($app['job_title']) ?></h3>
                            <p><?= htmlspecialchars($app['company_name']) ?></p>
                        </div>
                    </div>
                    
                    <div class="job-details">
                        <span class="location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($app['location']) ?></span>
                        <span class="salary"><i class="fas fa-money-bill-wave"></i> $<?= number_format($app['salary'], 2) ?></span>
                        <span class="job-type"><i class="fas fa-briefcase"></i> <?= htmlspecialchars($app['job_type']) ?></span>
                    </div>
                    
                    <div class="application-status <?= strtolower($app['status']) ?>">
                        <span class="status-badge"><?= htmlspecialchars($app['status']) ?></span>
                        <span class="application-date">Applied: <?= date('M d, Y', strtotime($app['applied_at'])) ?></span>
                    </div>
                    
                    <div class="application-actions">
                        <button class="btn view-details" onclick="viewApplication(<?= $app['application_id'] ?>)">
                            View Details
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    function viewApplication(applicationId) {
        window.location.href = 'application_details.php?id=' + applicationId;
    }
</script>

</body>
</html>
