<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Job Nepal</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/top.css">
    <link rel="stylesheet" href="../assets/css/popular.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        h1 {
            color: #007bff;
            font-size: 42px;
            margin-bottom: 20px;
        }

        .contact-info {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .contact-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 30%;
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }

        .contact-card:hover {
            transform: scale(1.05);
        }

        .contact-card i {
            font-size: 30px;
            color: #007bff;
            margin-bottom: 10px;
        }

        .contact-card h3 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .contact-card p {
            font-size: 16px;
            color: #555;
        }

        /* Contact Form */
        .contact-form {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            text-align: left;
            max-width: 600px;
            margin: auto;
        }

        .contact-form label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .contact-form button {
            display: block;
            width: 100%;
            background: #007bff;
            color: white;
            border: none;
            padding: 12px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 15px;
            transition: background 0.3s ease-in-out;
        }

        .contact-form button:hover {
            background: #0056b3;
        }

        /* Google Map */
        .map-container {
            margin-top: 30px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        iframe {
            width: 100%;
            height: 400px;
            border: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .contact-info {
                flex-direction: column;
                align-items: center;
            }

            .contact-card {
                width: 90%;
                margin-bottom: 15px;
            }

            .contact-form {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<?php include '../includes/header.php'; ?>

<section id="main">
    <div class="container">
        <h1>Contact <span style="color: #ff6f00;">Job Nepal</span></h1>

        <!-- Contact Information Cards -->
        <div class="contact-info">
            <div class="contact-card">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Our Address</h3>
                <p>Kathmandu, Nepal</p>
            </div>

            <div class="contact-card">
                <i class="fas fa-envelope"></i>
                <h3>Email Us</h3>
                <p>support@jobnepal.com</p>
            </div>

            <div class="contact-card">
                <i class="fas fa-phone"></i>
                <h3>Call Us</h3>
                <p>+977 123-456-7890</p>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form">
            <h2>Get In Touch</h2>
            <form action="process_contact.php" method="POST">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>

                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" required>

                <label for="message">Message</label>
                <textarea id="message" name="message" rows="5" required></textarea>

                <button type="submit">Send Message</button>
            </form>
        </div>

        <!-- Google Maps -->
        <div class="map-container">
            <h2>Find Us On The Map</h2>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3532.017446505802!2d85.32396071453172!3d27.71724588279278!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39eb19148c7780df%3A0x8c3a2e1e29b8a72b!2sKathmandu!5e0!3m2!1sen!2snp!4v1626920912657!5m2!1sen!2snp" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

</body>
</html>
