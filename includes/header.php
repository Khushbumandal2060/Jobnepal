<?php
// session_start(); 
?> 

<header>
<nav class="nav-container" style="">

        <div class="logo">
            <a style="text-decoration: none; color:white" href="/">/</a>
        </div>
        <div class="nav-links" style="display:flex; align-items:center">
           <a href="/job">Jobs</a>
            <a href="/companies">Companies</a>
            <a href="/info/about.php" class="">About Us</a>
            <a href="/info/contact.php" class="">Contact Us</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="dropdown profile-dropdown">
                    <a href="#" class="dropdown-button">
                        <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        <span class="dropdown-icon">↓</span>
                    </a>
                    <div class="dropdown-content-loggedin">
                        <a href="/auth/check.php"><svg class="icon-loggedin"></svg>Dashboard</a>
                        <a href="/auth/logout.php"><svg class="icon-loggedin"></svg>Log Out</a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Show login/register if user is not logged in -->
                <a href="/auth/login.php" class="btn btn-primary">Login</a>
                <a href="/auth/register.php" class="btn btn-secondary">Register</a>
                
            <?php endif; ?>
        </div>
    </nav>
</header>
