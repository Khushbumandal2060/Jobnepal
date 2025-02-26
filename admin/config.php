<?php
$host = "localhost";  // Database host
$username = "root";   // Database username
$password = "";       // Database password
$db_name = "job_portal";  // Database name

// Create a connection
$conn = mysqli_connect($host, $username, $password, $db_name);

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
