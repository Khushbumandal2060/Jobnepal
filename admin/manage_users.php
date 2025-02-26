<?php
// manage_users.php
session_start();
include '../auth/config.php';

// Check if user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /jobnepal/auth/login.php");
    exit;
}

// Function to delete a user
if (isset($_POST['delete_user'])) {
    $user_id_to_delete = (int)$_POST['delete_user'];

    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :user_id AND role = 'job_seeker'");
        $stmt->bindParam(':user_id', $user_id_to_delete, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'User deleted successfully.'];
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'User not found or not a job seeker.'];
        }

    } catch (PDOException $e) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error deleting user: ' . $e->getMessage()];
        error_log("Error deleting user: " . $e->getMessage());
    }
    header("Location: manage_users.php");
    exit;
}

try {
    $sql = "SELECT
                u.id,
                u.email,
                u.created_at,
                js.name,
                js.skills,
                js.experience,
                (SELECT COUNT(*) FROM job_applications ja WHERE ja.job_seeker_id = js.id) AS application_count
            FROM users u
            INNER JOIN job_seekers js ON u.id = js.user_id
            WHERE u.role = 'job_seeker'
            ORDER BY u.created_at DESC";

    $stmt = $pdo->query($sql);
    $jobSeekers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error fetching users: ' . $e->getMessage()];
    error_log("Error fetching users: " . $e->getMessage());
    $jobSeekers = [];
}
?>

<style>
    .user-card {
        display: inline-block;
        width: 400px; /* Fixed width */
        border: 1px solid #ddd;
        border-radius: 8px;
        margin: 10px;
        padding: 15px;
        text-align: left;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s ease-in-out;
        vertical-align: top; /* Added to align cards to the top */
    }

    .user-card:hover {
        transform: translateY(-5px);
    }

    .user-card h3 {
        margin-top: 0;
        color: #2c3e50;
        font-size: 1.3em;
    }

    .user-card p {
        margin-bottom: 10px;
        color: #555;
        line-height: 1.6;
    }

    .user-card strong {
        color: #34495e;
    }

    .delete-button {
        background-color: #e74c3c;
        color: white;
        border: none;
        padding: 10px 16px;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s ease;
        font-size: 0.9em;
    }

    .delete-button:hover {
        background-color: #c0392b;
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
    <h1>Manage Job Seekers</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="message <?= htmlspecialchars($_SESSION['message']['type']) ?>">
            <?= htmlspecialchars($_SESSION['message']['text']) ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (empty($jobSeekers)): ?>
        <p>No job seekers found.</p>
    <?php else: ?>
        <div style="display: flex; flex-wrap: wrap;">
            <?php foreach ($jobSeekers as $seeker): ?>
                <div class="user-card">
                    <h3><?= htmlspecialchars($seeker['name']) ?></h3>
                    <p>
                        <strong>Email:</strong> <?= htmlspecialchars($seeker['email']) ?><br>
                        <strong>Joined:</strong> <?= htmlspecialchars(date('Y-m-d', strtotime($seeker['created_at']))) ?><br>
                        <strong>Applications:</strong> <?= htmlspecialchars($seeker['application_count']) ?>
                    </p>

                    <?php if (!empty($seeker['skills'])): ?>
                        <p><strong>Skills:</strong> <?= htmlspecialchars($seeker['skills']) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($seeker['experience'])): ?>
                        <p><strong>Experience:</strong> <?= htmlspecialchars($seeker['experience']) ?></p>
                    <?php endif; ?>

                    <form method="post" action="manage_users.php">
                        <input type="hidden" name="delete_user" value="<?= htmlspecialchars($seeker['id']) ?>">
                        <button type="submit" class="delete-button" onclick="return confirm('Are you sure you want to delete this user?')">
                            Delete User
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>