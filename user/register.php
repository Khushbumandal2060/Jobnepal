<?php
include 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_type = $_POST['user_type'];
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Common validations
    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Determine user type specific fields
        if ($user_type == 'company') {
            $name = trim($_POST['company_name']);
            $table = 'companies';
            $name_field = 'name';
        } else {
            $name = trim($_POST['fullname']);
            $table = 'jobseekers';
            $name_field = 'fullname';
        }
        
        if (empty($name)) {
            $error = ($user_type == 'company') ? "Company name is required" : "Full name is required";
        } else {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM $table WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $error = "Email already registered";
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user
                $stmt = $pdo->prepare("INSERT INTO $table ($name_field, email, password) VALUES (?, ?, ?)");
                if ($stmt->execute([$name, $email, $hashed_password])) {
                    $success = "Registration successful! Please login.";
                    // Clear form values
                    $_POST = array();
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        }
    }
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
    <style>
        .signup-container {
            max-width: 500px;
            margin: 5rem auto;
            padding: 2rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .user-type-select {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .user-type-btn {
            flex: 1;
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }
        .user-type-btn.active {
            border-color: #0d6efd;
            background-color: #e7f1ff;
        }
        .dynamic-field {
            display: none;
        }
        .dynamic-field.active {
            display: block;
        }
    </style>
</head>
<body>
<?php include '../includes/header2.php'; ?>
    <div class="container">
        <div class="signup-container">
            <h2 class="text-center mb-4">Create Account</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form action="signup.php" method="post">
                <div class="user-type-select">
                    <div class="user-type-btn active" onclick="selectUserType('company')">
                        <input type="radio" name="user_type" value="company" checked hidden>
                        Company
                    </div>
                    <div class="user-type-btn" onclick="selectUserType('jobseeker')">
                        <input type="radio" name="user_type" value="jobseeker" hidden>
                        Job Seeker
                    </div>
                </div>

                <div class="mb-3 dynamic-field active" id="company-name-field">
                    <label for="company_name" class="form-label">Company Name</label>
                    <input type="text" class="form-control" id="company_name" name="company_name"
                           value="<?= htmlspecialchars($_POST['company_name'] ?? '') ?>">
                </div>

                <div class="mb-3 dynamic-field" id="fullname-field">
                    <label for="fullname" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="fullname" name="fullname"
                           value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" required
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Sign Up</button>
                
                <div class="mt-3 text-center">
                    Already have an account? 
                    <a href="login.php">Login here</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function selectUserType(type) {
            // Update active class on buttons
            document.querySelectorAll('.user-type-btn').forEach(btn => {
                btn.classList.remove('active');
                btn.previousElementSibling?.classList.remove('active');
            });
            event.target.classList.add('active');

            // Toggle fields visibility
            if (type === 'company') {
                document.getElementById('company-name-field').classList.add('active');
                document.getElementById('fullname-field').classList.remove('active');
                document.querySelector('input[name="user_type"][value="company"]').checked = true;
            } else {
                document.getElementById('fullname-field').classList.add('active');
                document.getElementById('company-name-field').classList.remove('active');
                document.querySelector('input[name="user_type"][value="jobseeker"]').checked = true;
            }
        }
    </script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>