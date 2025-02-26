<?php
session_start();
include '../auth/config.php';

// Check if user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /jobnepal/auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_company'])) {
    $company_id_to_delete = (int)$_POST['delete_company'];

    try {
        $pdo->beginTransaction();
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

        // Delete job applications related to this company's jobs
        $stmt = $pdo->prepare("DELETE FROM job_applications WHERE job_id IN (SELECT id FROM jobs WHERE company_id = :company_id)");
        $stmt->bindParam(':company_id', $company_id_to_delete, PDO::PARAM_INT);
        $stmt->execute();

        // Delete jobs associated with the company
        $stmt = $pdo->prepare("DELETE FROM jobs WHERE company_id = :company_id");
        $stmt->bindParam(':company_id', $company_id_to_delete, PDO::PARAM_INT);
        $stmt->execute();

        // Get the user_id of the company before deleting
        $stmt = $pdo->prepare("SELECT user_id FROM companies WHERE id = :company_id");
        $stmt->bindParam(':company_id', $company_id_to_delete, PDO::PARAM_INT);
        $stmt->execute();
        $company = $stmt->fetch(PDO::FETCH_ASSOC);

        // Delete the company
        $stmt = $pdo->prepare("DELETE FROM companies WHERE id = :company_id");
        $stmt->bindParam(':company_id', $company_id_to_delete, PDO::PARAM_INT);
        $stmt->execute();

        if ($company) {
            $user_id_to_delete = $company['user_id'];

            // Check if the user has any other companies
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM companies WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id_to_delete, PDO::PARAM_INT);
            $stmt->execute();
            $company_count = $stmt->fetchColumn();

            // If no other company exists for this user, delete the user
            if ($company_count == 0) {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = :user_id AND role = 'company'");
                $stmt->bindParam(':user_id', $user_id_to_delete, PDO::PARAM_INT);
                $stmt->execute();
            }
        }

        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        $pdo->commit();
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Company deleted successfully.'];
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error deleting company: ' . $e->getMessage()];
        error_log("Error deleting company: " . $e->getMessage());
    }
    header("Location: /jobnepal/admin");
    exit;
}