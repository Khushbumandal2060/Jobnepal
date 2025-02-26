<?php
// delete_user.php
session_start();
include '../auth/config.php'; // Database connection

// Ensure the request is POST and the delete_user field is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id_to_delete = (int)$_POST['delete_user'];

    try {
        $pdo->beginTransaction(); // Start transaction

        // Check if the user exists and is a job_seeker
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id AND role = 'job_seeker'");
        $stmt->bindParam(':user_id', $user_id_to_delete, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user) {
            // Delete job applications associated with this job seeker
            $stmt = $pdo->prepare("DELETE FROM job_applications WHERE job_seeker_id = :user_id");
            $stmt->bindParam(':user_id', $user_id_to_delete, PDO::PARAM_INT);
            $stmt->execute();

            // Delete the job_seeker profile
            $stmt = $pdo->prepare("DELETE FROM job_seekers WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id_to_delete, PDO::PARAM_INT);
            $stmt->execute();

            // Finally, delete the user account
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :user_id AND role = 'job_seeker'");
            $stmt->bindParam(':user_id', $user_id_to_delete, PDO::PARAM_INT);
            $stmt->execute();

            $pdo->commit(); // Commit transaction
            $_SESSION['message'] = ['type' => 'success', 'text' => 'User deleted successfully.'];
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'User not found or not a job seeker.'];
        }

    } catch (PDOException $e) {
        $pdo->rollBack(); // Rollback on error
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error deleting user: ' . $e->getMessage()];
        error_log("Error deleting user: " . $e->getMessage());
    }
}

header("Location: /jobnepal/admin");
exit;
