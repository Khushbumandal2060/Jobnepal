<?php


session_start();

function checkAuthentication() {
    // Check if the user is logged in
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
        // User is logged in, check their role
        $userType = $_SESSION['user_type'];

        if ($userType === 'company') {
            // User is a company, redirect to the company dashboard
            header("Location: /jobnepal/company");
            exit;
        } elseif ($userType === 'job_seeker') {
            // User is a job seeker, redirect to the job seeker dashboard
            header("Location: /jobnepal/user");
            exit;
        } else {
            session_unset();
            session_destroy();
            header("Location: /jobnepal/auth/login.php?message=Invalid session. Please login again.");
            exit;
        }
    } else {
       
        header("Location: /jobnepal/auth/login.php");
        exit;
    }
}

// Call the function to check authentication and redirect
checkAuthentication();
?>

