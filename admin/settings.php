<?php
session_start();
include '../auth/config.php';


$user_id = $_SESSION['user_id'];

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    try {
        // Verify current password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (password_verify($_POST['current_password'], $user['password'])) {
            if ($_POST['new_password'] === $_POST['confirm_password']) { 
                $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$new_password, $user_id]);

                $_SESSION['message'] = ['type' => 'success', 'text' => 'Password updated successfully!'];
            } else {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'New passwords do not match.'];
            }
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

    $_SESSION['email_notifications'] = isset($_POST['email_notifications']) ? true : false;
    $_SESSION['job_alerts'] = isset($_POST['job_alerts']) ? true : false;
    $_SESSION['application_updates'] = isset($_POST['application_updates']) ? true : false;


    $_SESSION['message'] = ['type' => 'success', 'text' => 'Preferences updated successfully!'];
}

// Function to check if a notification preference is enabled
function isNotificationEnabled($preferenceKey)
{
    return isset($_SESSION[$preferenceKey]) && $_SESSION[$preferenceKey] === true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

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

        .message.danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
    </style>
</head>

<body>
    <div class="settings-container">
        <h2 class="section-title">Account Settings</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div id="message-box" class="message <?php echo $_SESSION['message']['type']; ?>">
                <?php echo $_SESSION['message']['text']; ?>
            </div>
            <script>
                setTimeout(() => {
                    let messageBox = document.getElementById('message-box');
                    if (messageBox) {
                        messageBox.style.display = 'none';
                    }
                    window.location.href = "/jobnepal/company?page=settings";
                }, 2000);
            </script>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>


        <!-- Change Password Section -->
        <div class="settings-section">
            <h3>Change Password</h3>
            <form action="settings.php" method="POST" class="settings-form">
                <input type="hidden" name="action" value="change_password">

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <div class="password-field">
                        <input type="password" id="current_password" name="current_password" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('current_password')"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="password-field">
                        <input type="password" id="new_password" name="new_password" onkeyup="checkPasswordStrength()"
                            required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('new_password')"></i>
                    </div>
                    <div id="password-strength"></div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <div class="password-field">
                        <input type="password" id="confirm_password" name="confirm_password"
                            onkeyup="validatePassword()" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password')"></i>
                    </div>
                    <div id="password-match-message"></div>
                </div>

                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
        </div>
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

            if (password.length < 6) {
                strengthIndicator.textContent = "Weak 🔴";
                strengthIndicator.className = "weak";
            } else if (password.length < 10) {
                strengthIndicator.textContent = "Medium 🟠";
                strengthIndicator.className = "medium";
            } else {
                strengthIndicator.textContent = "Strong 🟢";
                strengthIndicator.className = "strong";
            }
        }

        function validatePassword() {
            const newPassword = document.getElementById("new_password").value;
            const confirmPassword = document.getElementById("confirm_password").value;
            const messageElement = document.getElementById("password-match-message");

            if (newPassword === confirmPassword) {
                messageElement.textContent = "Passwords match ✅";
                messageElement.className = "strong"; // You can define a CSS class for a green "match" message
                return true;
            } else {
                messageElement.textContent = "Passwords do not match ❌";
                messageElement.className = "weak"; // You can define a CSS class for a red "no match" message
                return false;
            }
        }
    </script>
</body>

</html>