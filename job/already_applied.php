<?php
// session_start(); // Uncomment if you're not starting the session elsewhere
include '../auth/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Already Applied</title>
</head>
<body>
    <h1>You have already applied for this job!</h1>
    <p>You can view your application status in your profile.</p>
    <a href="index.php">Back to Job Listings</a>
</body>
</html>