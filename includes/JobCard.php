<?php
// session_start();
include '../auth/config.php';

        function renderJobCard($job)
        {
            // Check if the user is logged in
            $isLoggedIn = isset($_SESSION['user_id']);
            $job_seeker_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Get user ID if logged in

            // Check if the user has already applied for this job
            $hasApplied = false;
            if ($isLoggedIn) {
                global $pdo; // Access the PDO connection
                $check_sql = "SELECT COUNT(*) FROM job_applications WHERE job_seeker_id = :job_seeker_id AND job_id = :job_id";
                try {
                    $check_stmt = $pdo->prepare($check_sql);
                    $check_stmt->bindParam(':job_seeker_id', $job_seeker_id, PDO::PARAM_INT);
                    $check_stmt->bindParam(':job_id', $job['job_id'], PDO::PARAM_INT);
                    $check_stmt->execute();
                    $application_count = $check_stmt->fetchColumn();
                    $hasApplied = ($application_count > 0);
                } catch (PDOException $e) {
                    // Handle the error appropriately (log it, display a message, etc.)
                    error_log("Error checking application status: " . $e->getMessage());
                    $hasApplied = false; // Assume not applied in case of error
                }
            }


            echo '
            <div class="job-card">
                <div class="job-card-header">
                    <div class="job-title-company">
                        <h3 class="job-title">' . htmlspecialchars($job['job_title']) . '</h3>
                        <span class="company-name">' . htmlspecialchars($job['company_name']) . '</span>
                    </div>
                </div>

                <div class="job-card-body">
                    <div class="job-details">
                        <span class="job-location"><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($job['job_location']) . '</span>
                        <span class="job-type"><i class="fas fa-briefcase"></i> ' . htmlspecialchars($job['job_type']) . '</span>
                        <p class="salary-range">RS ' . number_format($job['job_salary'], 2) . '</p>
                        <p class="job-description">' . htmlspecialchars(substr($job['job_description'], 0, 200)) . '...</p>
                    </div>
                </div>

                <div class="job-card-footer">
                    <div class="job-actions">
                        <a href="#" class="save-job">Save Job</a>';

            // Conditionally display "Apply Now" button or "Applied" status
            if ($isLoggedIn) {
                if ($hasApplied) {
                    echo '<span class="applied-status">Applied</span>';
                } else {
                    echo '<a href=/jobnepal/job/apply_job.php?job_id=' . $job['job_id'] . '" class="apply-now">Apply Now</a>';
                }
            } else {
                echo '<a href="login.php" class="apply-now">Login to Apply</a>';
            }

            echo '
                    </div>
                </div>
            </div>';
        }

        try {
            $stmt = $pdo->query("SELECT
                jobs.id AS job_id,
                jobs.title AS job_title,
                jobs.description AS job_description,
                jobs.location AS job_location,
                jobs.salary AS job_salary,
                jobs.job_type AS job_type,
                jobs.created_at AS job_created_at,
                companies.name AS company_name,
                companies.logo AS company_logo
            FROM jobs
            INNER JOIN companies ON jobs.company_id = companies.id
            ORDER BY jobs.created_at DESC");

            $jobs = $stmt->fetchAll();

            if ($jobs) {
                foreach ($jobs as $job) {
                    renderJobCard($job); // Call the render function for each job
                }
            } else {
                echo '<p class="no-jobs">No jobs found. Please check back later.</p>';
            }
        } catch (PDOException $e) {
            die("Error fetching jobs: " . $e->getMessage());
        }
        ?>

<style>
.job-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
    background-color: #fff;
}

.job-card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Header Styles */
.job-card-header {
    padding: 15px;
    display: flex;
    align-items: center;
    border-bottom: 1px solid #eee;
}

.company-logo {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
    margin-right: 15px;
    flex-shrink: 0; /* Prevent logo from shrinking */
}

.company-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.job-title-company {
    flex-grow: 1;
}

.job-title {
    font-size: 1.25rem;
    margin-bottom: 5px;
    color: #333;
}

.company-name {
    font-size: 0.9rem;
    color: #666;
}


/* Body Styles */
.job-card-body {
    padding: 15px;
}

.job-details {
    line-height: 1.6;
}

.job-location,
.job-type,
.salary-range {
    display: block;
    font-size: 0.9rem;
    color: #555;
    margin-bottom: 5px;
}

.job-location i,
.job-type i {
    margin-right: 5px;
}

.job-description {
    font-size: 0.95rem;
    color: #444;
}


/* Footer Styles */
.job-card-footer {
    padding: 15px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    background-color: #f9f9f9;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
}

.job-actions a {
    display: inline-block;
    padding: 8px 16px;
    margin-left: 10px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.save-job {
    border: 1px solid #007bff;
    color: #007bff;
}

.save-job:hover {
    background-color: #007bff;
    color: #fff;
}

.apply-now {
    background-color: #28a745;
    color: #fff;
}

.apply-now:hover {
    background-color: #218838;
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
        flex-direction: column;
        align-items: flex-start;
    }

    .company-logo {
        margin-bottom: 10px;
    }

    .job-actions {
        width: 100%;
    }

    .job-actions a {
        display: block;
        width: 100%;
        text-align: center;
        margin-left: 0;
        margin-top: 5px;
    }
}

.applied-status {
    color: green;
    font-weight: bold;
    margin-left: 10px;
}
</style>