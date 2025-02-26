<?php
// profile.php
session_start();
include '../auth/config.php';

// Check if $pdo is correctly initialized in config.php
if (!$pdo) {
    die("Failed to connect to the database. Check your database configuration.");
}

// Function to fetch the number of job openings for a company (using PDO)
function getJobOpeningCount($pdo, $companyId)
{
    $sql = "SELECT COUNT(*) AS job_count FROM jobs WHERE company_id = :company_id"; // Corrected parameter name
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':company_id', $companyId, PDO::PARAM_INT); // Corrected parameter name
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['job_count'] : 0;
    } catch (PDOException $e) {
        error_log("Error fetching job count: " . $e->getMessage());
        return 0;
    }
}

?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="../assets/css/top.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;

        }

        .company-card {
            transition: transform 0.2s;
            height: 100%;
        }

        .company-card:hover {
            transform: translateY(-5px);
        }

        .company-logo {
            width: 50px;
            height: 50px;
            /* object-fit: cover;*/
        }

        .company-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .company-img {
                width: 100%;
                height: 200px;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="container-fluid my-4">
        <?php include '../includes/tophiring.php'; ?>
    </div>

    <main class="container my-5">
        <div class="row g-4">
            <!-- Company Listings -->
            <div class="col-lg-12">
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php
                    // Fetch companies from the database (using PDO)
                    $sql = "SELECT id, name, company_website, company_description FROM companies";
                    try {
                        $stmt = $pdo->query($sql);
                        $companies = $stmt->fetchAll();

                        if ($companies) {
                            foreach ($companies as $row) {
                                $companyId = $row["id"];
                                $companyName = $row["name"];
                                $companyWebsite = $row["company_website"];
                                $companyDescription = $row["company_description"];
                                $jobOpenings = getJobOpeningCount($pdo, $companyId);

                                echo '
                                <div class="col">
                                    <div class="card company-card h-100 shadow-sm">
                                        <div class="row g-0">
                                            <div class="col-md-4 d-flex justify-content-center align-items-center">
                                                <i class="bi bi-building company-logo" style="font-size: 3rem;"></i>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="card-body">
                                                    <h5 class="card-title mb-3">' . htmlspecialchars($companyName) . '</h5>
                                                    <p class="card-text small mb-2">' . htmlspecialchars($companyDescription) . '</p>
                                                    <p class="card-text small mb-2">
                                                        <a href="' . htmlspecialchars($companyWebsite) . '" target="_blank">Visit Website</a>
                                                    </p>
                                                    <p class="card-text small mb-3">
                                                        <strong>Job Openings:</strong> ' . $jobOpenings . '
                                                    </p>
                                                    <a href="/jobnepal/job" class="btn btn-primary btn-sm">View Jobs</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>';
                            }
                        } else {
                            echo "<p>No companies found.</p>";
                        }
                    } catch (PDOException $e) {
                        error_log("Error fetching companies: " . $e->getMessage());
                        echo "<p>Error fetching companies. Please try again later.</p>";
                    }

                    ?>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php include '../includes/footer.php'; ?>
</body>

</html>