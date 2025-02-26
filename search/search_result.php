<?php
session_start();
include '../auth/config.php';

// Function to sanitize input
function sanitizeInput($data)
{
    return htmlspecialchars(trim($data));
}

// Get search terms from query string
$searchTerm = isset($_GET['term']) ? sanitizeInput($_GET['term']) : '';
$searchLocation = isset($_GET['location']) ? sanitizeInput($_GET['location']) : '';

// SQL query with conditions
$sql = "SELECT jobs.id AS job_id, jobs.title AS job_title, jobs.description AS job_description, 
        jobs.location AS job_location, jobs.salary AS job_salary, jobs.job_type AS job_type, 
        companies.name AS company_name, companies.logo AS company_logo FROM jobs 
        INNER JOIN companies ON jobs.company_id = companies.id WHERE 1=1";

if (!empty($searchTerm)) {
    $sql .= " AND jobs.title LIKE :term";
}
if (!empty($searchLocation)) {
    $sql .= " AND jobs.location LIKE :location";
}
$sql .= " ORDER BY jobs.created_at DESC";

// Prepare and execute query
try {
    $stmt = $pdo->prepare($sql);
    if (!empty($searchTerm)) {
        $stmt->bindValue(':term', "%$searchTerm%", PDO::PARAM_STR);
    }
    if (!empty($searchLocation)) {
        $searchTerm = $searchLocation;
        $stmt->bindValue(':location', "%$searchLocation%", PDO::PARAM_STR);
        
    }
    $stmt->execute();
    $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error executing search query: " . $e->getMessage());
    $searchResults = [];
}

// Get job seeker ID
function getJobSeekerId($pdo, $user_id)
{
    try {
        $stmt = $pdo->prepare("SELECT id FROM job_seekers WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Error fetching job_seeker_id: " . $e->getMessage());
        return null;
    }
}

// Check if user has applied for a job
function hasUserApplied($pdo, $job_seeker_id, $job_id)
{
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM job_applications WHERE job_seeker_id = :job_seeker_id AND job_id = :job_id");
        $stmt->bindParam(':job_seeker_id', $job_seeker_id, PDO::PARAM_INT);
        $stmt->bindParam(':job_id', $job_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        error_log("Error checking application status: " . $e->getMessage());
        return false;
    }
}

// Render job card
function renderJobCard($job, $isLoggedIn, $job_seeker_id)
{
    $hasApplied = $isLoggedIn && hasUserApplied($GLOBALS['pdo'], $job_seeker_id, $job['job_id']);

    echo "<div class='card job-card shadow-sm'>";
    echo "  <div class='card-body'>";
    echo "    <div class='d-flex align-items-center mb-3'>";
    echo "      <div>";
    echo "        <h5 class='card-title'>" . htmlspecialchars($job['job_title']) . "</h5>";
    echo "        <p class='card-subtitle text-muted'>" . htmlspecialchars($job['company_name']) . "</p>";
    echo "      </div>";
    echo "    </div>";
    echo "    <p class='card-text'><i class='fas fa-map-marker-alt'></i> " . htmlspecialchars($job['job_location']) . "</p>";
    echo "    <p class='card-text'><strong>Salary:</strong> RS " . number_format($job['job_salary'], 2) . "</p>";
    echo "    <p class='card-text'>" . htmlspecialchars(substr($job['job_description'], 0, 150)) . "...</p>";
    echo "  </div>";
    echo "  <div class='card-footer bg-transparent border-top d-flex justify-content-end'>";
    echo "    <div class='job-actions'>";
    if ($isLoggedIn) {
        echo $hasApplied ? "<span class='applied-status'>Applied</span>" : "<a href='/jobnepal/job/apply_job.php?job_id={$job['job_id']}' class='btn btn-primary btn-sm'>Apply Now</a>";
    } else {
        echo "<a href='/jobnepal/auth/login.php' class='btn btn-outline-primary btn-sm'>Login to Apply</a>";
    }
    echo "    </div>";
    echo "  </div>";
    echo "</div>";
}

$isLoggedIn = isset($_SESSION['user_id']);
$job_seeker_id = $isLoggedIn ? getJobSeekerId($pdo, $_SESSION['user_id']) : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results for "<?= htmlspecialchars($searchTerm) ?>"</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            flex: 1;
            margin-top: 30px;
            margin-bottom: 30px;
            /* Added margin for better spacing */
        }

        h1 {
            color: #343a40;
            margin-bottom: 20px;
            text-align: center;
        }

        .job-listings {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
        }

        .job-card {
            border: none;
            border-radius: 10px;
        }

        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
            /* Enhanced shadow on hover */
        }

        .company-logo {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }

        .card-title {
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
            /* Reduced margin */
        }

        .card-subtitle {
            font-size: 0.9rem;
        }

        .card-text {
            font-size: 1rem;
        }

        .job-actions a {
            padding: 8px 16px;
        }

        .applied-status {
            color: green;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            margin-top: auto;
            /* Stick footer to bottom */
        }
    </style>
</head>

<body>


    <?php include '../includes/header2.php'; ?>

    <div class="container">
        <h1>Search Results for "<?= htmlspecialchars($searchTerm) ?>"</h1>

        <?php if (empty($searchResults)): ?>
            <p class="text-center">No jobs found matching your criteria.</p>
        <?php else: ?>
            <div class="job-listings">
                <?php foreach ($searchResults as $job): ?>
                    <?php renderJobCard($job, $isLoggedIn, $job_seeker_id); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>