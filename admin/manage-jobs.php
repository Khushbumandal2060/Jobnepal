<?php
// manage-jobs.php
?>


<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle job deletion
if (isset($_GET['delete_job_id'])) {
    $job_id = $_GET['delete_job_id'];
    $query = "DELETE FROM jobs WHERE id = $job_id";
    mysqli_query($conn, $query);
    header("Location: manage_jobs.php");
    exit;
}

// Handle job editing
if (isset($_GET['edit_job_id'])) {
    $job_id = $_GET['edit_job_id'];
    $query = "SELECT * FROM jobs WHERE id = $job_id";
    $result = mysqli_query($conn, $query);
    $job = mysqli_fetch_assoc($result);
}

// Handle job updates
if (isset($_POST['update_job'])) {
    $job_id = $_POST['job_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $company_name = $_POST['company_name'];
    $location = $_POST['location'];

    $query = "UPDATE jobs SET title = '$title', description = '$description', company_name = '$company_name', location = '$location' WHERE id = $job_id";
    mysqli_query($conn, $query);
    header("Location: manage_jobs.php");
    exit;
}

// Handle job addition
if (isset($_POST['add_job'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $company_name = $_POST['company_name'];
    $location = $_POST['location'];
    $salary = $_POST['salary'];

    $query = "INSERT INTO jobs (title, description, company_name, location) VALUES ('$title', '$description', '$company_name', '$location', '$salary)";
    mysqli_query($conn, $query);
    header("Location: manage_jobs.php");
    exit;
}

// Fetch all jobs for display
$query = "SELECT * FROM jobs";
$jobs = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="manage-jobs-container">
    <h2>Manage Jobs</h2>

    <form action="manage_jobs.php" method="POST">
        <label for="title">Job Title:</label>
        <input type="text" name="title" required><br>

        <label for="description">Job Description:</label>
        <textarea name="description" required></textarea><br>

        <label for="company_name">Company Name:</label>
        <input type="text" name="company_name" required><br>

        <label for="location">Location:</label>
        <input type="text" name="location" required><br>

        <label for="salary">Salary:</label>
        <input type="text" name="location" required><br>

        <button type="submit" name="add_job">Add Job</button>
    </form>

    <h3>Existing Jobs</h3>
    <table>
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Company</th>
                <th>Location</th>
                <th>Description</th>
                <th>Salary</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($job = mysqli_fetch_assoc($jobs)): ?>
                <tr>
                    <td><?php echo $job['title']; ?></td>
                    <td><?php echo $job['company_name']; ?></td>
                    <td><?php echo $job['location']; ?></td>
                    <td>
                        <a href="manage_jobs.php?edit_job_id=<?php echo $job['id']; ?>">Edit</a> | 
                        <a href="manage_jobs.php?delete_job_id=<?php echo $job['id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
