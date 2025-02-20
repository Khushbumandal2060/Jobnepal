<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];
    
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        $table = ($user_type == 'company') ? 'companies' : 'jobseekers';
        
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user_type;
            $_SESSION['loggedin'] = true;
            
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid credentials";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Job Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="../assets/css/top.css" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
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
    </style>
</head>
<body>
<?php include '../includes/header2.php'; ?>
    <div class="container" style="min-height:70vh">
        <div class="login-container">
            <h2 class="text-center mb-4">Job Portal Login</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form action="login.php" method="post">
                <div class="user-type-select">
                    <div class="user-type-btn <?= ($_POST['user_type'] ?? '') == 'company' ? 'active' : '' ?>" 
                         onclick="document.querySelector('input[name=user_type][value=company]').checked = true; this.classList.add('active'); this.previousElementSibling?.classList.remove('active')">
                        <input type="radio" name="user_type" value="company" <?= ($_POST['user_type'] ?? '') == 'company' ? 'checked' : '' ?> hidden>
                        Company
                    </div>
                    <div class="user-type-btn <?= ($_POST['user_type'] ?? '') == 'jobseeker' ? 'active' : '' ?>" 
                         onclick="document.querySelector('input[name=user_type][value=jobseeker]').checked = true; this.classList.add('active'); this.previousElementSibling?.classList.remove('active')">
                        <input type="radio" name="user_type" value="jobseeker" <?= ($_POST['user_type'] ?? '') == 'jobseeker' ? 'checked' : '' ?> hidden>
                        Job Seeker
                    </div>
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

                <button type="submit" class="btn btn-primary w-100">Login</button>
                
                <div class="mt-3 text-center">
                    Don't have an account? 
                    <a href="register.php">Register here</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Add active class to selected user type
        document.querySelectorAll('.user-type-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.user-type-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>

<?php include '../includes/footer.php'; ?>
</body>
</html>