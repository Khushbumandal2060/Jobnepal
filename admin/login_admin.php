<?php
session_start();
require_once '../auth/config.php';

// Check if the user is logged in and redirect if they are
if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'admin') {
    header("Location: /jobnepal/admin");
    exit();
}

// Check if the email and password are provided via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {  // Changed from GET to POST
    // Get the email and password
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);  // Changed from GET to POST
    $password = $_POST['password'];  // Changed from GET to POST

    // Validate the email and password
    if (empty($email) || empty($password)) {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => "Please provide both email and password."
        ];
        // After setting the session message, redirect back to the login page
        header("Location: login.php"); //Changed header to main login page
        exit();
    }

    // Proceed with authentication if email and password are not empty
    try {
        // Query the users table for admin users, added inner join to also retrieve data from the admin role
        $stmt = $pdo->prepare("SELECT u.*, a.name as admin_name FROM users u INNER JOIN admin a ON u.id = a.user_id WHERE u.email = :email AND u.role = 'admin'");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['role'];
            // Use the name of the role
            $_SESSION['user_name'] = $user['admin_name'];

            $_SESSION['loggedin'] = true;

            $_SESSION['message'] = [
                'type' => 'success',
                'text' => "Login successful! Welcome back, " . htmlspecialchars($user['admin_name'])
            ];

            header("Location: /jobnepal/admin"); // Admin Dashboard
            exit();
        } else {
            // Authentication failed
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => "Invalid email or password."
            ];
             header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        // Database error
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => "Database error: " . $e->getMessage()
        ];
        error_log("Login error: " . $e->getMessage());
    }

    // Always redirect back to the login page after processing the login attempt
        header("Location: login.php");
    exit();
} else {
    // If the email and password are not provided, redirect back to the login page with an error message
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => "Please log in through the login form."
    ];
      header("Location: login.php");
    exit();
}
?>