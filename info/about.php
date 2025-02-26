<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Job Nepal</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 30px;
            text-align: center;
        }

        h1, h2 {
            color: #007bff;
        }

        h1 {
            font-size: 42px;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 28px;
            margin-top: 30px;
            border-bottom: 3px solid #007bff;
            display: inline-block;
            padding-bottom: 8px;
        }

        p {
            font-size: 18px;
            color: #555;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        ul li {
            font-size: 18px;
            color: #555;
            padding: 10px;
            display: flex;
            align-items: center;
        }

        ul li i {
            font-size: 22px;
            color: #007bff;
            margin-right: 10px;
        }

        /* Card Styling */
        .info-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            transition: transform 0.3s ease-in-out;
        }

        .info-card:hover {
            transform: scale(1.03);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<?php include '../includes/header.php'; ?>

<section id="main">
    <div class="container">
        <h1>About <span style="color: #ff6f00;">Job Nepal</span></h1>

        <div class="info-card">
            <p>Welcome to <b>Job Nepal</b>, your premier platform for connecting talented individuals with exciting career opportunities across Nepal. Our mission is to simplify the job search process for candidates and streamline recruitment for employers, fostering economic growth and prosperity throughout the nation.</p>
        </div>

        <div class="info-card">
            <h2>Our Vision</h2>
            <p>To be the leading job portal in Nepal, recognized for its comprehensive job listings, intuitive interface, and commitment to connecting the right people with the right opportunities.</p>
        </div>

        <div class="info-card">
            <h2>Our Values</h2>
            <ul>
                <li><i class="fas fa-check-circle"></i><b> Integrity:</b> We conduct our business with the highest ethical standards.</li>
                <li><i class="fas fa-lightbulb"></i><b> Innovation:</b> We are constantly seeking new ways to improve our platform and services.</li>
                <li><i class="fas fa-user-friends"></i><b> User-Centricity:</b> We prioritize the needs and experiences of our users.</li>
                <li><i class="fas fa-globe"></i><b> Community:</b> We are dedicated to contributing to the economic development of Nepal.</li>
            </ul>
        </div>

        <div class="info-card">
            <h2>What We Offer</h2>
            <ul>
                <li><i class="fas fa-briefcase"></i><b> Extensive Job Listings:</b> A wide range of job opportunities from various industries and locations across Nepal.</li>
                <li><i class="fas fa-user"></i><b> User-Friendly Interface:</b> An intuitive and easy-to-navigate platform for both job seekers and employers.</li>
                <li><i class="fas fa-filter"></i><b> Advanced Search & Filtering:</b> Powerful tools to help you find the perfect job or candidate.</li>
                <li><i class="fas fa-bullhorn"></i><b> Employer Branding:</b> Opportunities for companies to showcase their culture and attract top talent.</li>
                <li><i class="fas fa-book"></i><b> Career Resources:</b> Valuable tips, advice, and resources to help you succeed in your job search or recruitment efforts.</li>
            </ul>
        </div>

        <div class="info-card">
            <p style="font-weight: bold; font-size: 20px;">Thank you for choosing <span style="color: #ff6f00;">Job Nepal</span>. We are excited to be a part of your career journey or recruitment success!</p>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

</body>
</html>
