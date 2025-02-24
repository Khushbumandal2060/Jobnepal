<?php
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="YOUR_SRI_HASH" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* General Styles */
        :root {
            --primary-color: #12538f; /* A modern green */
            --secondary-color: #1565a7; /* Darker shade of green for accents */
            --tertiary-color: #f39c12; /* A warm orange */
            --text-color: #333;
            --light-text-color: #777;
            --background-color: #f9f9f9;
            --card-background: #fff;
            --border-radius: 8px;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
        }

        h2 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 2rem;
        }

        /* Applications Page */
        .applications-container {
            padding: 2rem;
        }

        .applications-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .application-card {
            background: var(--card-background);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: 0 2px 4px var(--shadow-color);
            transition: transform 0.3s ease;
            border-left: 5px solid var(--primary-color); /* Colorful left border */
        }

        .application-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px var(--shadow-color);
        }

        .company-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .company-logo {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%; /* Make logo circular */
            border: 2px solid var(--primary-color);
        }

        .job-details {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1rem;
            color: var(--light-text-color);
        }

        .job-details span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.pending { background: #fff3cd; color: #856404; }
        .status-badge.accepted { background: #d4edda; color: #155724; }
        .status-badge.rejected { background: #f8d7da; color: #721c24; }

        .application-date {
            font-size: 0.8rem;
            color: var(--light-text-color);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: 0 2px 4px var(--shadow-color);
        }

        .empty-state i {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #2980b9;
        }
        .salary {
            font-weight: bold; /* Make the salary stand out */
            color: var(--secondary-color); /* Use a different color for emphasis */
        }
    </style>
</head>
<body>

<div class="applications-container">
    <h2>My Applications</h2>

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
                        <div>
                            <h3><?= htmlspecialchars($app['job_title']) ?></h3>
                            <p><?= htmlspecialchars($app['company_name']) ?></p>
                        </div>
                    </div>

                    <div class="job-details">
                        <span class="location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($app['location']) ?></span>
                        <span class="salary"><i class="fas fa-money-bill-wave"></i> <span class="salary">Rs. <?= number_format($app['salary'], 2) ?></span></span>
                        <span class="job-type"><i class="fas fa-briefcase"></i> <?= htmlspecialchars($app['job_type']) ?></span>
                        <span class="application-date">Applied: <?= date('M d, Y', strtotime($app['applied_at'])) ?></span>
                    </div>

                    <div class="application-status <?= strtolower($app['status']) ?>">
                        <span class="status-badge <?= strtolower($app['status']) ?>"><?= htmlspecialchars($app['status']) ?></span>
                    </div>


                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>


</body>
</html>