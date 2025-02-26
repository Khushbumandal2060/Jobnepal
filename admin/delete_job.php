<?php
session_start();
include '../auth/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /jobnepal/auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_job'])) {
    $job_id_to_delete = (int)$_POST['delete_job'];

    try {
        $pdo->beginTransaction();

        // Disable foreign key checks temporarily
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

        // Delete job applications associated with the job
        $stmt = $pdo->prepare("DELETE FROM job_applications WHERE job_id = :job_id");
        $stmt->bindParam(':job_id', $job_id_to_delete, PDO::PARAM_INT);
        $stmt->execute();

        // Delete saved jobs associated with the job
        $stmt = $pdo->prepare("DELETE FROM saved_jobs WHERE job_id = :job_id");
        $stmt->bindParam(':job_id', $job_id_to_delete, PDO::PARAM_INT);
        $stmt->execute();

        // Delete the job
        $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = :job_id");
        $stmt->bindParam(':job_id', $job_id_to_delete, PDO::PARAM_INT);
        $stmt->execute();

        // Re-enable foreign key checks
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

        $pdo->commit();
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Job deleted successfully.'];

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error deleting job: ' . $e->getMessage()];
        error_log("Error deleting job: " . $e->getMessage());
    }
}

header("Location: /jobnepal/admin");
exit;
