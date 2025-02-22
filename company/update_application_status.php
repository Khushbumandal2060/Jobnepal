<?php
session_start();
include 'auth/config.php';
include 'auth/check_company_login.php';

$company_id = $_SESSION['company_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['application_id']) && isset($_POST['status'])) {
    $application_id = $_POST['application_id'];
    $status = $_POST['status'];

    // Verify that the application belongs to a job posted by this company
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM applications
        INNER JOIN jobs ON applications.job_id = jobs.id
        WHERE applications.id = ? AND jobs.company_id = ?
    ");
    $stmt->execute([$application_id, $company_id]);

    if ($stmt->fetchColumn() == 0) {
        http_response_code(403); // Forbidden
        echo "You do not have permission to update this application.";
        exit;
    }

    // Update the status
    try {
        $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
        $stmt->execute([$status, $application_id]);
        echo "Status updated successfully";
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo "Error updating status: " . $e->getMessage();
    }
} else {
    http_response_code(400); // Bad Request
    echo "Invalid request.";
}
?>