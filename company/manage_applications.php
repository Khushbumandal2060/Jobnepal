<?php
session_start();
include '../auth/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    header("Location: ../auth/login.php");
    exit;
}

$job_id = $_GET['job_id'] ?? null;

if (!$job_id) {
    echo "Job ID not provided.";
    exit;
}

try {
    // Fetch job details
    $stmt = $pdo->prepare("
        SELECT * FROM jobs 
        WHERE id = ? AND company_id = ?
    ");
    $stmt->execute([$job_id, $_SESSION['company_id']]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$job) {
        echo "Job not found or access denied.";
        exit;
    }

    // Fetch applications with applicant details
    $stmt = $pdo->prepare("
        SELECT 
            ja.id as application_id,
            ja.status,
            ja.applied_at,
            js.name as applicant_name,
            js.profile_pic,
            js.resume,
            js.skills,
            js.experience
        FROM job_applications ja
        JOIN job_seekers js ON ja.job_seeker_id = js.id
        WHERE ja.job_id = ?
        ORDER BY ja.applied_at DESC
    ");
    $stmt->execute([$job_id]);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle status updates
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'], $_POST['status'])) {
        $stmt = $pdo->prepare("
            UPDATE job_applications 
            SET status = ? 
            WHERE id = ? AND job_id = ?
        ");
        $stmt->execute([$_POST['status'], $_POST['application_id'], $job_id]);

        header("Location: manage_applications.php?job_id=" . $job_id);
        exit;
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo "An error occurred. Please try again.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Job Applications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="YOUR_SRI_HASH" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* General Styles */
        :root {
            --primary-color: #673ab7;
            --secondary-color: #512da8;
            --tertiary-color: #f39c12;
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
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
        }

        .application-card {
            background: var(--card-background);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: 0 2px 4px var(--shadow-color);
            transition: transform 0.3s ease;
            border-left: 5px solid var(--primary-color);
            position: relative;
            /* For status badge positioning */
        }

        .application-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px var(--shadow-color);
        }

        .applicant-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .applicant-photo {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid var(--primary-color);
        }

        .applicant-details {
            margin-bottom: 1.5rem;
        }

        .applicant-details h4 {
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }

        .skills,
        .experience,
        .cover-letter {
            margin-bottom: 1rem;
        }

        .application-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .status-form {
            display: inline-block;
        }

        .status-select {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            border: 1px solid #ddd;
            font-size: 1rem;
            cursor: pointer;
        }

        /* Status Colors */
        .status-select.pending {
            color: #856404;
        }

        .status-select.accepted {
            color: #155724;
        }

        .status-select.rejected {
            color: #721c24;
        }

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

        /* Modal Styles */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            /* Black w/ opacity */
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            position: relative;
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: var(--border-radius);
        }

        .close {
            position: absolute;
            top: 0;
            right: 0;
            padding: 10px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .resume-content {
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        /* Status Badge */
        .status-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-badge.accepted {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge.rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .application-date {
            font-size: 0.8rem;
            color: var(--light-text-color);
            display: block;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .applications-grid {
                grid-template-columns: 1fr;
                /* Stack cards on smaller screens */
            }

            .application-card {
                padding: 1rem;
            }

            .applicant-info {
                flex-direction: column;
                align-items: flex-start;
            }

            .application-actions {
                flex-direction: column;
                align-items: stretch;
                gap: 0.5rem;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
</head>

<body>
    <div class="applications-container">
        <h2 class="section-title">Applications for <?= htmlspecialchars($job['title']) ?></h2>

        <?php if (empty($applications)): ?>
            <div class="empty-state">
                <i class="fas fa-file-alt fa-3x"></i>
                <p>No applications received yet.</p>
            </div>
        <?php else: ?>
            <div class="applications-grid">
                <?php foreach ($applications as $application): ?>
                    <div class="application-card">
                        <span
                            class="status-badge <?= strtolower($application['status']) ?>"><?= htmlspecialchars($application['status']) ?></span>
                        <div class="applicant-info">
                            <div>
                                <h3><?= htmlspecialchars($application['applicant_name']) ?></h3>
                                <span class="application-date">Applied:
                                    <?= date('M d, Y', strtotime($application['applied_at'])) ?></span>
                            </div>
                        </div>

                        <div class="applicant-details">
                            <div class="skills">
                                <h4>Skills</h4>
                                <p><?= htmlspecialchars($application['skills']) ?></p>
                            </div>

                            <div class="experience">
                                <h4>Experience</h4>
                                <p><?= htmlspecialchars($application['experience']) ?></p>
                            </div>
                        </div>

                        <div class="application-actions">
                            <form method="POST" class="status-form" action="update_application_status.php">
                                <input type="hidden" name="application_id" value="<?= $application['application_id'] ?>">
                                <input type="hidden" name="job_id" value="<?= $job_id ?>"> <!-- VERY IMPORTANT -->
                                <select name="status" onchange="this.form.submit()"
                                    class="status-select <?= strtolower($application['status']) ?>">
                                    <option value="pending" <?= $application['status'] === 'pending' ? 'selected' : '' ?>>Pending
                                    </option>
                                    <option value="accepted" <?= $application['status'] === 'accepted' ? 'selected' : '' ?>>Accept
                                    </option>
                                    <option value="rejected" <?= $application['status'] === 'rejected' ? 'selected' : '' ?>>Reject
                                    </option>
                                </select>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function viewResume(resume) {
            // Create a modal to display the resume
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.innerHTML = `
        <div class="modal-content">
            <span class="close">×</span>
            <h3>Resume</h3>
            <div class="resume-content">${resume}</div>
        </div>
    `;
            document.body.appendChild(modal);

            // Close button functionality
            const closeBtn = modal.querySelector('.close');
            closeBtn.onclick = function () {
                modal.remove();
            }

            // Close modal when clicking outside
            window.onclick = function (event) {
                if (event.target === modal) {
                    modal.remove();
                }
            }

            // Show the modal
            modal.style.display = "flex";
        }
    </script>
</body>

</html>