<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JOBNepal - Job Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/top.css">
    <link rel="stylesheet" href="assets/css/popular.css">
    <link rel="stylesheet" href="assets/css/search.css">
</head>

<body style="max-width:100%; overflow-x:hidden;">
    <?php include 'includes/header.php'; ?>
    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1>Find Your Dream Job in Nepal</h1>
                <p>Discover thousands of job opportunities across Nepal</p>
                <?php
                // Ensure the correct file path
                if (file_exists('includes/search.php')) {
                    include 'includes/search.php';

                    if (function_exists('renderSearchForm')) {
                        echo renderSearchForm(['action' => 'search_results.php']); // Correct action file
                    } else {
                        echo "Error: renderSearchForm() function not found in includes/search.php";
                    }
                } else {
                    echo "Error: includes/search.php not found";
                }
                ?>

            </div>
        </section>

        <?php include 'includes/categories.php'; ?>
        <?php include 'includes/tophiring.php'; ?>

        <img src="https://images.unsplash.com/photo-1521791136064-7986c2920216?q=80&w=2069&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
            alt="Internet" style="width:50%; max-height:40vh;  display: block; margin: 0 auto;">
        <?php include 'includes/popular.php'; ?>
        <?php include 'includes/showcard.php'; ?>
    </main>



    <?php include 'includes/footer.php'; ?>

</body>

</html>