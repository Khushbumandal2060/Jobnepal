<?php
// manage-users.php
?>

<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle user deletion
if (isset($_GET['delete_user_id'])) {
    $user_id = $_GET['delete_user_id'];
    $query = "DELETE FROM users WHERE id = $user_id";
    mysqli_query($conn, $query);
    header("Location: manage_users.php");
    exit;
}

// Fetch all users for display
$query = "SELECT * FROM users";
$users = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="manage-users-container">
    <h2>Manage Users</h2>

    <h3>Existing Users</h3>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td><?php echo $user['username']; ?></td>
                    <td>
                        <a href="manage_users.php?delete_user_id=<?php echo $user['id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
