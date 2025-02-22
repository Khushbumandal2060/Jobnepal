<?php
session_start();
include 'auth/config.php';
include 'auth/check_company_login.php';

$company_id = $_SESSION['company_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $description = $_POST['description'];

    // Logo upload handling (simplified example)
    $logo_path = null;
    if ($_FILES['logo']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/company_logos/"; // Create this directory
        $target_file = $target_dir . basename($_FILES["logo"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["logo"]["tmp_name"]);
        if($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["logo"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                $logo_path = $target_file; // Store the file path in the database
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Update the company profile in the database
    try {
        $stmt = $pdo->prepare("UPDATE companies SET name = ?, email = ?, description = ?, logo = COALESCE(?, logo) WHERE id = ?");
        $stmt->execute([$name, $email, $description, $logo_path, $company_id]);

        header("Location: company_dashboard.php?message=Profile updated successfully");
        exit;

    } catch (PDOException $e) {
        header("Location: company_dashboard.php?error=Error updating profile");
        exit;
    }
} else {
    header("Location: company_dashboard.php");
    exit;
}
?>