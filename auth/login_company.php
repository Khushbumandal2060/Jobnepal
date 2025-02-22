<?php
session_start();
require_once 'config.php';

// Check if the user is logged in and redirect if they are
if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'company') {
    header("Location: /jobnepal/auth/check.php");
    exit();
}

// Check if the email and password are provided via GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['email']) && isset($_GET['password'])) {
    // Get the email and password
    $email = filter_var(trim($_GET['email']), FILTER_SANITIZE_EMAIL);
    $password = $_GET['password'];

    // Validate the email and password
    if (empty($email) || empty($password)) {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => "Please provide both email and password."
        ];
        // After setting the session message, redirect back to the login page
        header("Location: login.php");
        exit();
    }

    // Proceed with authentication if email and password are not empty
    try {
        // Query the users table for job seekers
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'company'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['role'];

            $stmt = $pdo->prepare("SELECT id, name FROM companies WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            $company = $stmt->fetch();
            $_SESSION['user_name'] = $company['name'];

            $_SESSION['loggedin'] = true;

            $_SESSION['message'] = [
                'type' => 'success',
                'text' => "Login successful! Welcome back, " . htmlspecialchars($company['name'])
            ];

            header("Location: /jobnepal/auth/check.php"); // Job Seeker Dashboard
            exit();
        } else {
            // Authentication failed
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => "Invalid email or password."
            ];
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