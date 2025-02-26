<?php
session_start();
include '../auth/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /jobnepal/auth/login.php");
    exit();
}

// if (!isset($_GET['job_id']) || !is_numeric($_GET['job_id'])) {
//     header("Location: /jobnepal/job/index.php");
//     exit();
// }

$job_id = (int) $_GET['job_id'];
$user_id = $_SESSION['user_id'];

try {
    // 🔹 Get the corresponding job_seeker_id from job_seekers table
    $seeker_sql = "SELECT id FROM job_seekers WHERE user_id = :user_id";
    $seeker_stmt = $pdo->prepare($seeker_sql);
    $seeker_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $seeker_stmt->execute();
    $job_seeker_id = $seeker_stmt->fetchColumn();

    // 🛑 Check if the user is a valid job seeker
    if (!$job_seeker_id) {
        die("Error: No corresponding job_seeker_id found. Please complete your job seeker profile.");
    }

    // 🔹 Check if user has already applied
    $check_sql = "SELECT COUNT(*) FROM job_applications WHERE job_seeker_id = :job_seeker_id AND job_id = :job_id";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->bindParam(':job_seeker_id', $job_seeker_id, PDO::PARAM_INT);
    $check_stmt->bindParam(':job_id', $job_id, PDO::PARAM_INT);
    $check_stmt->execute();

    if ($check_stmt->fetchColumn() > 0) {
        header("Location: /jobnepal/job/already_applied.php");
        exit();
    }

    // 🔹 Insert into job_applications
    $sql = "INSERT INTO job_applications (job_seeker_id, job_id) VALUES (:job_seeker_id, :job_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':job_seeker_id', $job_seeker_id, PDO::PARAM_INT);
    $stmt->bindParam(':job_id', $job_id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: /jobnepal/job/application_success.php");
    exit();
} catch (PDOException $e) {
    die("Error applying for job: " . $e->getMessage());
}

?>
