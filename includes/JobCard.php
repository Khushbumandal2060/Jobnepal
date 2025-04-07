<?php
include '../auth/config.php';

/**
 * Fetch the job seeker ID from the job_seekers table.
 */
function getJobSeekerId($pdo, $user_id)
{
    $seeker_sql = "SELECT id FROM job_seekers WHERE user_id = :user_id";
    try {
        $stmt = $pdo->prepare($seeker_sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Error fetching job_seeker_id: " . $e->getMessage());
        return null;
    }
}

/**
 * Check if a job seeker has already applied for a job.
 */
function hasUserApplied($pdo, $job_seeker_id, $job_id)
{
    $check_sql = "SELECT COUNT(*) FROM job_applications WHERE job_seeker_id = :job_seeker_id AND job_id = :job_id";
    try {
        $stmt = $pdo->prepare($check_sql);
        $stmt->bindParam(':job_seeker_id', $job_seeker_id, PDO::PARAM_INT);
        $stmt->bindParam(':job_id', $job_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        error_log("Error checking application status: " . $e->getMessage());
        return false;
    }
}

/**
 * Render a single job card.
 */
function renderJobCard($job, $isLoggedIn, $job_seeker_id)
{
    $hasApplied = $isLoggedIn && hasUserApplied($GLOBALS['pdo'], $job_seeker_id, $job['job_id']);

    echo '<div class="job-card">
        <div class="job-card-header">
            <h3 class="job-title">' . htmlspecialchars($job['job_title']) . '</h3>
            <span class="company-name">' . htmlspecialchars($job['company_name']) . '</span>
        </div>
        <div class="job-card-body">
            <span class="job-location"><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($job['job_location']) . '</span>
            <span class="job-type"><i class="fas fa-briefcase"></i> ' . htmlspecialchars($job['job_type']) . '</span>
            <p class="salary-range">RS ' . number_format($job['job_salary'], 2) . '</p>
            <p class="job-description">' . htmlspecialchars(substr($job['job_description'], 0, 200)) . '</p>
            <p class="job-skills"><strong>Skills:</strong> ' . htmlspecialchars($job['skills']) . '</p>
            <p class="job-experience"><strong>Experience:</strong> ' . htmlspecialchars($job['experience']) . '</p>
        </div>
        <div class="job-card-footer">
            <div class="job-actions">';
               
    if ($isLoggedIn) {
        echo $hasApplied ? '<span class="applied-status">Applied</span>' 
                         : '<a href="/jobnepal/job/apply_job.php?job_id=' . $job['job_id'] . '" class="apply-now">Apply Now</a>';
    } else {
        echo '<a href="/jobnepal/auth/login.php" class="apply-now">Login to Apply</a>';
    }

    echo '</div></div></div>';
}

/**
 * Fetch all job listings.
 */
try {
    $stmt = $pdo->query("SELECT jobs.id AS job_id, jobs.title AS job_title, jobs.description AS job_description, jobs.location AS job_location, jobs.salary AS job_salary, jobs.job_type AS job_type, jobs.skills AS skills, jobs.experience AS experience, companies.name AS company_name FROM jobs INNER JOIN companies ON jobs.company_id = companies.id ORDER BY jobs.created_at DESC");
    $jobs = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching jobs: " . $e->getMessage());
}

// Get logged-in user info
$isLoggedIn = isset($_SESSION['user_id']);
$job_seeker_id = $isLoggedIn ? getJobSeekerId($pdo, $_SESSION['user_id']) : null;

// Render job listings
if ($jobs) {
    foreach ($jobs as $job) {
        renderJobCard($job, $isLoggedIn, $job_seeker_id);
    }
} else {
    echo '<p class="no-jobs">No jobs found. Please check back later.</p>';
}
?>
<style>/* Job Card Container */
.job-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
    background-color: #fff;
    overflow: hidden;
}

.job-card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Header Styles */
.job-card-header {
    padding: 15px;
    display: flex;
    flex-direction: column;
    border-bottom: 1px solid #eee;
    background-color: #f5f5f5;
}

.job-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.company-name {
    font-size: 1rem;
    color: #666;
}

/* Body Styles */
.job-card-body {
    padding: 15px;
}

.job-location,
.job-type,
.salary-range {
    display: block;
    font-size: 0.9rem;
    color: #555;
    margin-bottom: 5px;
}

.job-description,
.job-skills,
.job-experience {
    font-size: 0.95rem;
    color: #444;
    margin-bottom: 5px;
}

.job-skills strong,
.job-experience strong {
    color: #333;
}

/* Footer Styles */
.job-card-footer {
    padding: 15px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    background-color: #f9f9f9;
}

.job-actions a {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: background-color 0.3s ease, color 0.3s ease;
    margin-left: 10px;
}

.apply-now {
    background-color: #28a745;
    color: #fff;
}

.apply-now:hover {
    background-color: #218838;
}

.applied-status {
    color: green;
    font-weight: bold;
    margin-left: 10px;
}

.no-jobs {
    text-align: center;
    padding: 20px;
    font-style: italic;
    color: #777;
}

/* Responsive Design */
@media (max-width: 768px) {
    .job-card-header {
        align-items: flex-start;
    }

    .job-actions a {
        display: block;
        width: 100%;
        text-align: center;
        margin-left: 0;
        margin-top: 5px;
    }
}
</style>