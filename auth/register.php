<?php
session_start();
require_once 'config.php';

// Redirect logged-in users
if (!empty($_SESSION['user_id'])) {
    header("Location: /jobnepal"); // Or redirect to their dashboard
    exit;
}

// Retrieve and clear form data from the session, if available
$form_data = $_SESSION['form_data'] ?? [];
$user_type = $form_data['user_type'] ?? 'job_seeker'; // Default
$email = $form_data['email'] ?? '';
$name = $form_data['name'] ?? '';  // Use for both company and job seeker registration
unset($_SESSION['form_data']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize input
    $user_type = $_POST['user_type'];
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $name = trim($_POST['name']);  // Use same name for both companies and job seekers

    // Validation
    $errors = [];

    if (empty($email) || empty($password) || empty($confirm_password) || empty($name)) {
        $errors[] = "All fields are required.";
    }

    $allowed_user_types = ['job_seeker', 'company'];
    if (!in_array($user_type, $allowed_user_types)) {
        $errors[] = "Invalid user type selected.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['message'] = [
                    'type' => 'danger',
                    'text' => "Email already registered. Please use a different email."
                ];
            } else {
                // Insert into users table
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");

                if ($stmt->execute([$email, $hashed_password, $user_type])) {
                    $user_id = $pdo->lastInsertId();

                    // Insert into job_seekers or companies table based on user type
                    if ($user_type == 'job_seeker') {
                        // Insert default values for job_seekers
                        $stmt = $pdo->prepare("INSERT INTO job_seekers (user_id, name) VALUES (?, ?)");
                         if ($stmt->execute([$user_id, $name])) {
                            // Registration successful, so redirect the user to the jobseeker login page
                            $_SESSION['message'] = [
                                'type' => 'success',
                                'text' => "Registration successful! Please login as a Job Seeker to continue."
                            ];
                            header("Location: login.php");
                            exit();
                         }
                    } elseif ($user_type == 'company') {
                        // Insert default values for companies
                        $stmt = $pdo->prepare("INSERT INTO companies (user_id, name) VALUES (?, ?)");
                        if ($stmt->execute([$user_id, $name])) {
                            // Registration successful, so redirect the user to the company login page
                           $_SESSION['message'] = [
                                'type' => 'success',
                                'text' => "Registration successful! Please login as a Company to continue."
                            ];
                            header("Location: login.php");
                            exit();
                        }
                    } else {
                        $_SESSION['message'] = [
                            'type' => 'danger',
                            'text' => "Invalid user type. Registration failed."
                        ];
                    }

                    // Redirect after successful registration
                } else {
                    $_SESSION['message'] = [
                        'type' => 'danger',
                        'text' => "Registration failed. Please try again."
                    ];
                }
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => "Database error: " . $e->getMessage() // Display the error message
            ];
            error_log("Registration error: " . $e->getMessage());
        }
    } else {
        // Collect and store all validation error messages in the session
        $errors = [];
        if (empty($email) || empty($password) || empty($confirm_password) || empty($name)) {
            $errors[] = "All fields are required.";
        }
        if (!in_array($user_type, ['job_seeker', 'company'])) {
            $errors[] = "Invalid user type selected.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters.";
        }
        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }

        if (!empty($errors)) {
            // Store all errors into a single session message
            $messageText = implode("<br>", $errors);
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => $messageText
            ];
        }

        if (isset($_SESSION['message']) && $_SESSION['message']['type'] === 'danger') {
            $_SESSION['form_data'] = $_POST;
            header("Location: register.php");
            exit;
        }
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Job Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="../assets/css/top.css" rel="stylesheet">
    <!-- Include the auth.css -->
    <link href="../assets/css/auth.css" rel="stylesheet">
    <style>

    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <div class="auth-container">
        <h2 class="text-center mb-4">Create Account</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message']['type'] ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['message']['text'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <form method="post" novalidate>
            <div class="user-type-toggle">
                <button type="button" class="toggle-button active" id="jobseeker-toggle" data-user-type="job_seeker">Job Seeker</button>
                <button type="button" class="toggle-button" id="company-toggle" data-user-type="company">Company</button>
            </div>

            <input type="hidden" name="user_type" id="user_type" value="job_seeker">

            <div class="mb-3">
                <label class="form-label">
                    <span id="name-label"><?= $user_type === 'company' ? 'Company Name' : 'Full Name' ?></span>
                </label>
                <input type="text" class="form-control" name="name" required
                       value="<?= htmlspecialchars($name) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required
                       value="<?= htmlspecialchars($email) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required
                       minlength="8">
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="confirm_password" required
                       minlength="8">
            </div>

            <button type="submit" class="btn btn-primary w-100">Sign Up</button>

            <div class="text-center">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jobseekerToggle = document.getElementById('jobseeker-toggle');
        const companyToggle = document.getElementById('company-toggle');
        const userTypeInput = document.getElementById('user_type');
        const nameLabel = document.getElementById('name-label');

        function setActive(element) {
            jobseekerToggle.classList.remove('active');
            companyToggle.classList.remove('active');
            element.classList.add('active');
            userTypeInput.value = element.getAttribute('data-user-type');

            // Update label based on selected user type
            nameLabel.textContent = element.getAttribute('data-user-type') === 'company' ? 'Company Name' : 'Full Name';
        }

        jobseekerToggle.addEventListener('click', function() {
            setActive(this);
        });

        companyToggle.addEventListener('click', function() {
            setActive(this);
        });

        setActive(jobseekerToggle); // Set Job Seeker as default
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function (e) {
        const password = document.querySelector('input[name="password"]');
        const confirmPassword = document.querySelector('input[name="confirm_password"]');

        if (password.value !== confirmPassword.value) {
            e.preventDefault();
            alert('Passwords do not match!');
        }

        if (password.value.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long!');
        }
    });

    // Alert dismissal
    document.addEventListener('DOMContentLoaded', function () {
        const alertElement = document.querySelector('.alert');

        if (alertElement) {
            // Fade out and remove alert after 5 seconds
            setTimeout(function () {
                alertElement.style.transition = 'opacity 0.5s';
                alertElement.style.opacity = '0';
                setTimeout(function () {
                    alertElement.style.display = 'none';
                }, 500);
            }, 5000);
        }
    });

</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>