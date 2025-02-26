<?php
session_start();
require_once 'config.php';

// Redirect if already logged in
if (!empty($_SESSION['user_id'])) {
    if ($_SESSION['user_type'] === 'job_seeker') {
        header("Location: /jobnepal/auth/check.php");
    } elseif ($_SESSION['user_type'] === 'company') {
        header("Location: /jobnepal/auth/check.php");
    }
    exit;
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
    <!-- Include the auth.css -->
    <link href="../assets/css/auth.css" rel="stylesheet">
    <style>

    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <div class="auth-container">
        <h2 class="text-center mb-4">Job Portal Login</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message']['type'] ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['message']['text'] ?>
                <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <form action="#" method="post" id="loginForm" novalidate>
            <div class="user-type-toggle">
                <button type="button" class="toggle-button active" id="jobseeker-toggle" data-user-type="job_seeker">Job Seeker</button>
                <button type="button" class="toggle-button" id="company-toggle" data-user-type="company">Company</button>
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

            <div class="text-center">
                Don't have an account?
                <a href="register.php">Register here</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jobseekerToggle = document.getElementById('jobseeker-toggle');
        const companyToggle = document.getElementById('company-toggle');
        const form = document.getElementById('loginForm');

        function setActive(element) {
            // Remove active class from all buttons
            jobseekerToggle.classList.remove('active');
            companyToggle.classList.remove('active');

            // Add active class to the selected button
            element.classList.add('active');
        }

        function redirectToCorrectPage() {
            // Get the email and password from the form
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            // Get the selected user type from the button's data attribute
            let userType;
            if (jobseekerToggle.classList.contains('active')) {
                userType = 'job_seeker';
            } else {
                userType = 'company';
            }

            // Form the data into a query string
            const formData = `?email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`;

            // Form the data into a query string
            const formData2 = `?email=${email}&password=${password}`;

            // Redirect to the correct page based on user type
            if (userType === 'job_seeker') {
                window.location.href = 'login_jobseeker.php' + formData;
            } else {
                window.location.href = 'login_company.php' + formData;
            }
        }

        // Add event listeners to the buttons
        jobseekerToggle.addEventListener('click', function(event) {
            // Prevent form submission when the button is clicked
            event.preventDefault();

            // Remove active class from all buttons and set the current button as active
            setActive(this);
        });

        companyToggle.addEventListener('click', function(event) {
            // Prevent default form submission when the button is clicked
            event.preventDefault();

            // Remove active class from all buttons and set the current button as active
            setActive(this);
        });

        form.addEventListener('submit', function (event) {
            // Prevent default form submission behavior
            event.preventDefault();

            // Redirect to the correct page
            redirectToCorrectPage();
        });

        // Select Job Seeker as active on load
        setActive(jobseekerToggle);
    });

    // Auto-hide alerts after 5 seconds
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