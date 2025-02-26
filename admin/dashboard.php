<?php
// dashboard.php
?>

<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="dashboard-container">
    <h2>Welcome, Admin</h2>
    <a href="login.php?logout=true">Logout</a>
    
    <div class="dashboard-links">
        <h3>Manage Jobs</h3>
        <a href="manage_jobs.php">Go to Job Management</a>
        
        <h3>Manage Users</h3>
        <a href="manage_users.php">Go to User Management</a>
    </div>
</div>

</body>
</html>

<?php
// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
