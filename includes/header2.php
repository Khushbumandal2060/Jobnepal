<!-- <?php
session_start(); // Start the session
?> -->

<header>
<nav class="nav-container" style="">

        <div class="logo">
            <a style="text-decoration: none; color:white" href="/">JOBNepal</a>
        </div>
        <div class="nav-links" style="display:flex; align-items:center">
            <a href="/jobnepal/jobs">Jobs</a>
            <a href="/jobnepal/companies">Companies</a>

            <a href="#notifications" class="notification-icon" style="background-color:#11111141; border-radius:10%">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
            </a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="dropdown profile-dropdown">
                    <a href="#" class="dropdown-button">
                        <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        <span class="dropdown-icon">↓</span>
                    </a>
                    <div class="dropdown-content-loggedin">
                        <a href="/jobnepal/auth/check.php"><svg class="icon-loggedin"></svg>Dashboard</a>
                        <a href="/jobnepal/auth/logout.php"><svg class="icon-loggedin"></svg>Log Out</a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Show login/register if user is not logged in -->
                <a href="/jobnepal/auth/login.php" class="btn btn-primary">Login</a>
                <a href="/jobnepal/auth/register.php" class="btn btn-primary">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</header>
