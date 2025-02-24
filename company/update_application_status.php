<?php
session_start();
include '../auth/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    header("Location: ../auth/login.php");
    exit;
}

$company_id = $_SESSION['company_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['application_id'], $_POST['status'], $_POST['job_id'])) {
    $application_id = $_POST['application_id'];
    $status = $_POST['status'];
    $job_id = $_POST['job_id'];

    // Verify that the application belongs to a job posted by this company
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM job_applications ja
        INNER JOIN jobs j ON ja.job_id = j.id
        WHERE ja.id = ? AND j.company_id = ?
    ");
    $stmt->execute([$application_id, $company_id]);

    if ($stmt->fetchColumn() == 0) {
        http_response_code(403); // Forbidden
        echo "You do not have permission to update this application.";
        exit;
    }

    // Update the status
    try {
        $stmt = $pdo->prepare("UPDATE job_applications SET status = ? WHERE id = ?");
        $stmt->execute([$status, $application_id]);

        // Redirect back to the applications page
        header("Location: /jobnepal/company" );
        exit;
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo "Error updating status: " . $e->getMessage();
    }
} else {
    http_response_code(400); // Bad Request
    echo "Invalid request.";
}
?>