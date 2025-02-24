<?php
session_start();
include '../auth/config.php';

// Check if user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    http_response_code(403); // Forbidden
    echo "Unauthorized";
    exit;
}

if (isset($_GET['job_id']) && is_numeric($_GET['job_id'])) {
    $job_id = $_GET['job_id'];

    try {
        // Verify that the company owns the job before deleting
        $stmt = $pdo->prepare("SELECT company_id FROM jobs WHERE id = ?");
        $stmt->execute([$job_id]);
        $job = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$job) {
            http_response_code(404); // Not found
            echo "Job not found";
            exit;
        }

        $company_id = $_SESSION['company_id']; // Get company ID from session
        if ($job['company_id'] != $company_id) {
            http_response_code(403); // Forbidden
            echo "You are not authorized to delete this job.";
            exit;
        }

        // Delete the job
        $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
        $stmt->execute([$job_id]);

        // Check if the deletion was successful
        if ($stmt->rowCount() > 0) {
            http_response_code(200); // OK
            echo "Job deleted successfully";
        } else {
            http_response_code(500); // Internal Server Error
            echo "Failed to delete job";
        }

    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        error_log("Database error: " . $e->getMessage());
        echo "A database error occurred.";
    }
} else {
    http_response_code(400); // Bad Request
    echo "Invalid job ID";
}
?>