<?php
// company_profile.php
session_start();
include '../auth/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch company data
    $stmt = $pdo->prepare("
        SELECT c.*, u.email 
        FROM companies c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle logo upload
        $logo_path = null; // Initialize logo_path
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $filename = $_FILES['logo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $upload_path = '../uploads/company_logos/';
                $new_filename = uniqid() . '.' . $ext;
                $full_upload_path = $upload_path . $new_filename; // Correct path for move_uploaded_file
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $full_upload_path)) {
                    $logo_path = $full_upload_path;
                } else {
                    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Failed to upload logo.'];
                    header("Location: company_profile.php");
                    exit;
                }
            } else {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Invalid file type. Only JPG, JPEG, and PNG are allowed.'];
                header("Location: company_profile.php");
                exit;
            }
        }

        // Update company profile
        $stmt = $pdo->prepare("
            UPDATE companies 
            SET name = ?, 
                company_website = ?, 
                company_description = ?,
                logo = CASE WHEN ? IS NOT NULL THEN ? ELSE logo END  
            WHERE user_id = ?
        ");
        $stmt->execute([
            $_POST['name'],
            $_POST['website'],
            $_POST['description'],
            $logo_path, // Conditional logo update
            $logo_path,
            $user_id
        ]);

        $_SESSION['message'] = ['type' => 'success', 'text' => 'Company profile updated successfully!'];
        header("Location: company_profile.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'An error occurred. Please try again.'];
}
?>

    <style>
        .profile-container {
            max-width: 800px;
        margin: 20px auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }

        .logo-upload {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .current-logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 1px solid #ddd;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="text"],
        input[type="email"],
        input[type="url"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-top: 5px;
            font-size: 16px;
        }

        textarea {
            resize: vertical; /* Allows vertical resizing */
        }

        .upload-button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            border: none;
        }

        .upload-button:hover {
            background-color: #367C39;
        }

        input[type="file"] {
            display: none;
        }

        button {
            background-color: #007BFF;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .profile-container {
                width: 95%;
            }

            .current-logo {
                width: 100px;
                height: 100px;
            }
        }
    </style>

    <div class="profile-container">
        <h2 class="section-title">Company Profile</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?= $_SESSION['message']['type'] ?>">
                <?= $_SESSION['message']['text'] ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <form action="company_profile.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Company Logo</label>
                <div class="logo-upload">
                    <img src="<?= htmlspecialchars($company['logo'] ?? '../assets/images/default-company.png') ?>" alt="Company Logo" class="current-logo">
                    <label for="file-upload" class="upload-button">Upload Logo</label>
                    <input id="file-upload" type="file" name="logo" accept="image/*" />
                </div>
            </div>

            <div class="form-group">
                <label for="name">Company Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($company['name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" value="<?= htmlspecialchars($company['email']) ?>" disabled>
            </div>

            <div class="form-group">
                <label for="website">Website</label>
                <input type="url" id="website" name="website" value="<?= htmlspecialchars($company['company_website']) ?>">
            </div>

            <div class="form-group">
                <label for="description">Company Description</label>
                <textarea id="description" name="description" rows="6"><?= htmlspecialchars($company['company_description']) ?></textarea>
            </div>

            <button type="submit">Save Changes</button>
        </form>
    </div>