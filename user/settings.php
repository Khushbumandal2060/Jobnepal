<?php
// settings.php
session_start();
include '../auth/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'job_seeker') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    try {
        // Verify current password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (password_verify($_POST['current_password'], $user['password'])) {
            // Update to new password
            $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$new_password, $user_id]);

            $_SESSION['message'] = ['type' => 'success', 'text' => 'Password updated successfully!'];
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Current password is incorrect.'];
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'An error occurred. Please try again.'];
    }
}

// Handle notification preferences
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_preferences') {
    // Update notification preferences (you'll need to create a preferences table)
    $_SESSION['message'] = ['type' => 'success', 'text' => 'Preferences updated successfully!'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Job Applications</title>
    <style>
        .settings-container {
        min-width: 30vw;
        margin: auto;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        }

        .settings-section {
        background: white;
        padding: 2rem;
        border-radius: var(--border-radius);
        box-shadow: 0 2px 4px var(--shadow-color);
        margin-bottom: 2rem;
        }

        .settings-section h3 {
        margin-bottom: 1.5rem;
        color: var(--secondary-color);
        position: relative;
        padding-bottom: 0.5rem;
        }

        .settings-section h3::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 2px;
        background-color: var(--primary-color);
        }

        .settings-form {
        max-width: 500px;
        }

        .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        padding: 0.5rem 0;
        }

        .checkbox-label input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        }

        .danger-zone {
        border: 1px solid #dc3545;
        padding: 1.5rem;
        }

        .danger-zone h3 {
        color: #dc3545;
        }

        .danger-zone p {
        color: #666;
        margin-bottom: 1rem;
        }

        .btn-danger {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: var(--border-radius);
        cursor: pointer;
        transition: background-color 0.3s ease;
        }

        .btn-danger:hover {
        background-color: #c82333;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {

        .profile-container,
        .settings-container {
        padding: 1rem;
        }

        .profile-form,
        .settings-section {
        padding: 1.5rem;
        }

        .job-details {
        flex-direction: column;
        gap: 0.5rem;
        }

        .application-card {
        padding: 1rem;
        }

        .company-info {
        flex-direction: column;
        text-align: center;
        }

        .status-badge {
        width: 100%;
        text-align: center;
        }
        }

        /* Common Button Styles */
        .btn {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        border-radius: var(--border-radius);
        font-weight: 500;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        }

        .btn-primary {
        background-color: var(--primary-color);
        color: white;
        }

        .btn-primary:hover {
        background-color: var(--secondary-color);
        }

        /* Success/Error Message Styles */
        .message {
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: var(--border-radius);
        font-weight: 500;
        }

        .message.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        }

        .message.error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        }

        /* Animation Effects */
        @keyframes fadeIn {
        from {
        opacity: 0;
        transform: translateY(10px);
        }

        to {
        opacity: 1;
        transform: translateY(0);
        }
        }

        .application-card,
        .profile-form,
        .settings-section {
        animation: fadeIn 0.3s ease-out;
        }

        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f5f5f5;
                margin: 0;
                padding: 0;
            }

            .settings-container {
                max-width: 600px;
                margin: 50px auto;
                background: white;
                padding: 2rem;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            h2 {
                text-align: center;
                color: #333;
            }

            .form-group {
                margin-bottom: 1.5rem;
            }

            .form-group label {
                display: block;
                font-weight: bold;
                margin-bottom: 0.5rem;
            }

            .password-field {
                position: relative;
            }

            .password-field input {
                width: 100%;
                padding: 10px;
                padding-right: 40px;
                border: 1px solid #ccc;
                border-radius: 5px;
                font-size: 1rem;
            }

            .toggle-password {
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
                color: #777;
            }

            .toggle-password:hover {
                color: #333;
            }

            #password-strength {
                margin-top: 5px;
                font-size: 0.9rem;
                font-weight: 500;
            }

            .weak {
                color: red;
            }

            .medium {
                color: orange;
            }

            .strong {
                color: green;
            }

            #password-match-message {
                display: block;
                margin-top: 5px;
                font-size: 0.9rem;
                font-weight: 500;
            }

            .btn {
                display: block;
                width: 100%;
                padding: 10px;
                background-color: #007bff;
                color: white;
                border: none;
                border-radius: 5px;
                font-size: 1rem;
                cursor: pointer;
                transition: background 0.3s ease;
            }

            .btn:hover {
                background-color: #0056b3;
            }

            .message {
                padding: 1rem;
                margin-bottom: 1rem;
                border-radius: 5px;
                font-weight: bold;
                text-align: center;
            }

            .message.success {
                background-color: #d4edda;
                color: #155724;
                border: 1px solid #c3e6cb;
            }

            .message.error {
                background-color: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
            }
        </style>
</head>

<body>
    <div class="settings-container">
        <h2 class="section-title">Account Settings</h2>

        <!-- Change Password Section -->
        <div class="settings-section">
            <h3>Change Password</h3>
            <form action="settings.php" method="POST" class="settings-form">
                <input type="hidden" name="action" value="change_password">

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
        </div>

        <!-- Notification Preferences -->
        <div class="settings-section">
            <h3>Notification Preferences</h3>
            <form action="settings.php" method="POST" class="settings-form">
                <input type="hidden" name="action" value="update_preferences">

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="email_notifications" checked>
                        Receive email notifications
                    </label>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="job_alerts" checked>
                        Job alerts for matching positions
                    </label>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="application_updates" checked>
                        Application status updates
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">Save Preferences</button>
            </form>
        </div>

        <!-- Account Deletion -->
        <div class="settings-section danger-zone">
            <h3>Danger Zone</h3>
            <p>Once you delete your account, there is no going back. Please be certain.</p>
            <button class="btn btn-danger" onclick="confirmAccountDeletion()">Delete Account</button>
        </div>
    </div>


    <script>
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const icon = input.nextElementSibling;
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }

        function checkPasswordStrength() {
            const password = document.getElementById("new_password").value;
            const strengthIndicator = document.getElementById("password-strength");

            strengthIndicator.textContent = password.length < 6 ? "Weak 🔴" :
                password.length < 10 ? "Medium 🟠" : "Strong 🟢";
        }

        function validatePassword() {
            const newPassword = document.getElementById("new_password").value;
            const confirmPassword = document.getElementById("confirm_password").value;
            document.getElementById("password-match-message").textContent =
                newPassword === confirmPassword ? "Passwords match ✅" : "Passwords do not match ❌";
            return newPassword === confirmPassword;
        }
    </script>

</body>

</html>