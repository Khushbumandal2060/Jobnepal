<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JOBNepal - Job Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/top.css">
    <link rel="stylesheet" href="assets/css/popular.css">
</head>
<body style="max-width:100%; overflow-x:hidden;">
<?php include 'includes/header.php'; ?>
<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Find Your Dream Job in Nepal</h1>
            <p>Discover thousands of job opportunities across Nepal</p>
            <div class="search-container">
                <form class="search-form">
                <div style="position: relative; flex-grow: 1; min-width: 250px;">
        <i class="fa fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 1rem;"></i>
        <input type="text" 
               placeholder="Job title or keyword" 
               style="width: 100%;
                      padding: 0.875rem 1rem 0.875rem 2.5rem;
                      border: 1px solid #e5e7eb;
                      border-radius: 0.5rem;
                      font-size: 1rem;
                      color: #374151;
                      background: #f9fafb;
                      transition: all 0.2s ease;">
    </div>

    <div style="position: relative; flex-grow: 1; min-width: 250px;">
        <i class="fa fa-map-marker" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 1rem;"></i>
        <input type="text" 
               placeholder="Location" 
               style="width: 100%;
                      padding: 0.875rem 1rem 0.875rem 2.5rem;
                      border: 1px solid #e5e7eb;
                      border-radius: 0.5rem;
                      font-size: 1rem;
                      color: #374151;
                      background: #f9fafb;
                      transition: all 0.2s ease;">
    </div>

                    <button type="submit" class="search-button">Search Jobs</button>
                </form>
            </div>
        </div>
    </section>
    
    <?php include 'includes/categories.php'; ?>
    <?php include 'includes/tophiring.php'; ?>
   
    <img src="https://images.unsplash.com/photo-1521791136064-7986c2920216?q=80&w=2069&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" 
         alt="Internet"
         style="width:50%; max-height:40vh;  display: block; margin: 0 auto;">
    <?php include 'includes/popular.php'; ?>
    <?php include 'includes/showcard.php'; ?>
</main>



<?php include 'includes/footer.php'; ?>

</body>
</html>
