<?php
session_start();
include '../auth/config.php'; // Ensure database connection is included
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings - Find Your Dream Job</title>
    <meta name="description" content="Browse our curated list of job opportunities. Find your ideal career and apply today!">

    <!-- Favicon (Replace with your actual favicon) -->
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">

    <!-- External CSS -->
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/top.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f0f4f8;
            color: #333;
        }

        main {
            flex: 1;
            padding: 30px;
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
        }

        .section-title {
            font-size: 2.2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
        }

      
        /* Add some visual interest to the empty card area */
        .empty-card-placeholder {
            width: 100px;
            height: 100px;
            background-color: #eee;
            border-radius: 8px;
            margin-right: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #ccc;
            /* Add an icon here if you have one */
        }


    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="container-fluid my-4">
        <?php include '../includes/tophiring.php'; ?>

        <?php
        if (file_exists('../search/search.php')) {
            include '../search/search.php';

            if (function_exists('renderSearchForm')) {
                echo renderSearchForm(['action' => 'search_results.php']);
            } else {
                echo "Error: renderSearchForm() function not found in includes/search.php";
            }
        } else {
            echo "Error: includes/search.php not found";
        }
        ?>
    </div>

    <main>
        <h2 class="section-title">Explore Job Opportunities</h2>
        <?php include '../includes/JobCard.php'; ?>

    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>

</html>