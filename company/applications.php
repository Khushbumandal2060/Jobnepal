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
            ja.cover_letter,
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
                    <div class="applicant-info">
                        <img src="<?= htmlspecialchars($application['profile_pic'] ?? '../assets/images/default-profile.png') ?>" 
                             alt="<?= htmlspecialchars($application['applicant_name']) ?>" class="applicant-photo">
                        <div>
                            <h3><?= htmlspecialchars($application['applicant_name']) ?></h3>
                            <p class="application-date">Applied: <?= date('M d, Y', strtotime($application['applied_at'])) ?></p>
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

                        <div class="cover-letter">
                            <h4>Cover Letter</h4>
                            <p><?= htmlspecialchars($application['cover_letter']) ?></p>
                        </div>
                    </div>

                    <div class="application-actions">
                        <form method="POST" class="status-form">
                            <input type="hidden" name="application_id" value="<?= $application['application_id'] ?>">
                            <select name="status" onchange="this.form.submit()" class="status-select <?= strtolower($application['status']) ?>">
                                <option value="pending" <?= $application['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="accepted" <?= $application['status'] === 'accepted' ? 'selected' : '' ?>>Accept</option>
                                <option value="rejected" <?= $application['status'] === 'rejected' ? 'selected' : '' ?>>Reject</option>
                            </select>
                        </form>
                        <button class="btn btn-primary view-resume" 
                                onclick="viewResume('<?= htmlspecialchars($application['resume']) ?>')">
                            View Resume
                        </button>
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
            <span class="close">&times;</span>
            <h3>Resume</h3>
            <div class="resume-content">${resume}</div>
        </div>
    `;
    document.body.appendChild(modal);

    // Close button functionality
    const closeBtn = modal.querySelector('.close');
    closeBtn.onclick = function() {
        modal.remove();
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.remove();
        }
    }
}
</script>