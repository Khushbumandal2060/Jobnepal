<?php
// manage_jobs.php
session_start();
include '../auth/config.php';

// Check if user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /jobnepal/auth/login.php");
    exit;
}


// Fetch all jobs
try {
    $sql = "SELECT
                j.id,
                j.title,
                j.description,
                j.location,
                j.salary,
                j.job_type,
                j.created_at,
                c.name AS company_name,
                (SELECT COUNT(*) FROM job_applications ja WHERE ja.job_id = j.id) AS application_count
            FROM jobs j
            INNER JOIN companies c ON j.company_id = c.id
            ORDER BY j.created_at DESC";

    $stmt = $pdo->query($sql);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error fetching jobs: ' . $e->getMessage()];
    error_log("Error fetching jobs: " . $e->getMessage());
    $jobs = [];
}
?>

<style>
    .job-card {
        display: inline-block;
        width: 400px; /* Fixed width */
        border: 1px solid #ddd;
        border-radius: 10px;
        margin: 10px;
        padding: 20px;
        text-align: left;
        background-color: #f8f9fa;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease;
        vertical-align: top;
    }

    .job-card:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .job-card h3 {
        color: #343a40;
        font-size: 1.5rem;
        margin-bottom: 10px;
    }

    .job-card p {
        color: #6c757d;
        margin-bottom: 8px;
    }

    .job-card strong {
        color: #495057;
    }

    .job-card .description {
        margin-top: 15px;
        font-style: italic;
        color: #777;
    }

    .job-card .salary {
        font-weight: bold;
        color: #28a745;
    }

    .job-card .delete-button {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-top: 15px;
    }

    .job-card .delete-button:hover {
        background-color: #c82333;
    }

    .message {
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 5px;
    }

    .message.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .message.danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<div style="width: 80%; margin: 20px; text-align: left;">
    <h1>Manage Jobs</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="message <?= htmlspecialchars($_SESSION['message']['type']) ?>">
            <?= htmlspecialchars($_SESSION['message']['text']) ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (empty($jobs)): ?>
        <p>No jobs found.</p>
    <?php else: ?>
        <div style="display: flex; flex-wrap: wrap;">
            <?php foreach ($jobs as $job): ?>
                <div class="job-card">
                    <h3><?= htmlspecialchars($job['title']) ?></h3>

                    <p>
                        <strong>Company:</strong> <?= htmlspecialchars($job['company_name']) ?><br>
                        <strong>Location:</strong> <?= htmlspecialchars($job['location']) ?><br>
                        <strong>Type:</strong> <?= htmlspecialchars($job['job_type']) ?><br>
                        <strong>Created:</strong> <?= htmlspecialchars(date('Y-m-d', strtotime($job['created_at']))) ?><br>
                        <strong>Applications:</strong> <?= htmlspecialchars($job['application_count']) ?>
                    </p>

                    <p class="salary">
                        <strong>Salary:</strong> Rs.<?= htmlspecialchars(number_format($job['salary'], 2)) ?>
                    </p>

                    <?php if (!empty($job['description'])): ?>
                        <p class="description">
                            <strong>Description:</strong> <?= htmlspecialchars($job['description']) ?>
                        </p>
                    <?php endif; ?>

                    <form method="post"  action="delete_job.php" >
                        <input type="hidden" name="delete_job" value="<?= htmlspecialchars($job['id']) ?>">
                        <button type="submit" class="delete-button" onclick="return confirm('Are you sure you want to delete this job and all associated applications?')">
                            Delete Job
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>