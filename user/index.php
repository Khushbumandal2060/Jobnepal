<?php
include 'config.php';

if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: login.php");
    exit;
}

$user_type = $_SESSION['user_type'];
$user_id = $_SESSION['user_id'];

// Get user data
$table = ($user_type == 'company') ? 'companies' : 'jobseekers';
$stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Job Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Job Portal</a>
            <div class="d-flex align-items-center">
                <span class="me-3">Welcome, <?= $user['name'] ?? $user['fullname'] ?></span>
                <a href="logout.php" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Dashboard</h2>
        <div class="row mt-4">
            <?php if ($user_type == 'company'): ?>
                <!-- Company Dashboard Content -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Post New Job</h5>
                            <p class="card-text">Create a new job listing</p>
                            <a href="post-job.php" class="btn btn-primary">Post Job</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Job Seeker Dashboard Content -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Search Jobs</h5>
                            <p class="card-text">Find your next opportunity</p>
                            <a href="jobs.php" class="btn btn-primary">Search Jobs</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>