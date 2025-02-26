<?php
session_start();

// Destroy the session and all associated data
session_unset();
session_destroy();

// Redirect to the login page
header("Location: /jobnepal/");
exit;
?>