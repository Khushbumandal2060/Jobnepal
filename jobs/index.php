<?php
session_start();
include '../auth/config.php';
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings</title>
    <!-- Link to external CSS -->
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/top.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
            color: #333;
        }

        main {
            flex: 1;
            padding: 20px;
            width: 80%;
            max-width: 1200px;
            margin: 20px auto;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }

        .col-lg-3 {
            width: 25%;
            padding: 0 15px;
            box-sizing: border-box; /* Ensure padding doesn't affect width */
        }

        .col-lg-9 {
            width: 75%;
            padding: 0 15px;
            box-sizing: border-box; /* Ensure padding doesn't affect width */
        }

        /* Filter Section */
        .filter-section {
            background: #fff;
            border-radius: 8px;
            position: sticky;
            top: 20px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .filter-section h5 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #444;
        }

        .filter-section h6 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #555;
        }

        .form-check {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .form-check input[type="checkbox"] {
            margin-right: 8px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .form-check label {
            font-size: 14px;
            color: #666;
            cursor: pointer;
        }

        /* Job Listings */
        .job-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            border-left: 4px solid transparent;
            margin-bottom: 20px;
        }

        .job-card:hover {
            transform: translateY(-3px);
            border-left-color: #007bff;
        }

        .card-body {
            padding: 20px;
        }

        .company-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 5px;
            margin-right: 15px;
        }

        .job-title {
            font-size: 18px;
            margin-bottom: 5px;
            color: #333;
        }

        .job-meta {
            font-size: 14px;
            color: #777;
            margin-bottom: 10px;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            margin-right: 5px;
            color: #fff;
        }

        .bg-primary {
            background-color: #007bff;
        }

        .bg-secondary {
            background-color: #6c757d;
        }

        .urgent-badge {
            background: #dc3545;
        }

        .featured-badge {
            background: #ffc107;
            color: #333; /* Keep the text readable on the yellow background */
        }

        .salary-range {
            color: #28a745;
            font-weight: 500;
            font-size: 14px;
        }

        .job-description {
            font-size: 15px;
            color: #555;
            margin-bottom: 15px;
        }

        .job-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .job-actions small {
            color: #888;
            font-size: 13px;
        }

        .job-actions a {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .job-actions a:hover {
            background-color: #0056b3;
        }

        .job-actions a.outline {
            background-color: transparent;
            border: 1px solid #007bff;
            color: #007bff;
        }

        .job-actions a.outline:hover {
            background-color: #e9ecef;
            color: #0056b3;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .col-lg-3 {
                width: 100%;
            }

            .col-lg-9 {
                width: 100%;
            }

            .row {
                margin: 0;
            }

            .col-lg-3,
            .col-lg-9 {
                padding: 0;
            }

            main {
                width: 95%;
                margin: 10px auto;
            }

            .filter-section {
                position: static;
                margin-bottom: 20px;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header2.php'; ?>

    <div class="container-fluid my-4">
    <?php include '../includes/tophiring.php'; ?>
    <?php
                if (file_exists('../includes/search.php')) {
                    include '../includes/search.php';

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

    <main>
        <div class="row">
            <!-- Filters Column -->
            <div class="col-lg-3">
                <div class="filter-section">
                    <h5>Filter Jobs</h5>

                    <div class="mb-4">
                        <h6>Job Type</h6>
                        <?php
                        $jobTypes = [
                            'Full-time' => 2345,
                            'Part-time' => 876,
                            'Contract' => 543,
                            'Remote' => 1567
                        ];

                        foreach ($jobTypes as $type => $count) {
                            echo "<div class='form-check'>
                                    <input type='checkbox' id='type-$type'>
                                    <label for='type-$type'>$type ($count)</label>
                                  </div>";
                        }
                        ?>
                    </div>

                    <div class="mb-4">
                        <h6>Experience Level</h6>
                        <?php
                        $experienceLevels = [
                            'Entry Level' => 945,
                            'Mid Level' => 1567,
                            'Senior Level' => 723,
                            'Executive' => 189
                        ];

                        foreach ($experienceLevels as $level => $count) {
                            echo "<div class='form-check'>
                                    <input type='checkbox' id='level-$level'>
                                    <label for='level-$level'>$level ($count)</label>
                                  </div>";
                        }
                        ?>
                    </div>

                    <div class="mb-4">
                        <h6>Salary Range</h6>
                        <?php
                        $salaryRanges = [
                            '$30k - $50k' => 845,
                            '$50k - $80k' => 1345,
                            '$80k - $120k' => 967,
                            '$120k+' => 345
                        ];

                        foreach ($salaryRanges as $range => $count) {
                            echo "<div class='form-check'>
                                    <input type='checkbox' id='range-$range'>
                                    <label for='range-$range'>$range ($count)</label>
                                  </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Job Listings -->
            <div class="col-lg-9">
                <?php
                $jobs = [
                    [
                        'title' => 'Senior Software Engineer',
                        'company' => 'Tech Innovators Inc',
                        'location' => 'New York, NY',
                        'type' => 'Full-time',
                        'salary' => '$120,000 - $150,000',
                        'experience' => '5+ years',
                        'logo' => 'https://placehold.co/100x100',
                        'urgent' => true,
                        'posted' => '2d ago',
                        'description' => 'Lead development of next-gen cloud platforms. Requires expertise in AWS and microservices architecture.'
                    ],
                    [
                        'title' => 'Marketing Manager',
                        'company' => 'Digital Solutions Co',
                        'location' => 'Remote',
                        'type' => 'Contract',
                        'salary' => '$80,000 - $100,000',
                        'experience' => '3-5 years',
                        'logo' => 'https://placehold.co/100x100',
                        'featured' => true,
                        'posted' => '1d ago',
                        'description' => 'Drive digital marketing strategies for global clients. SEO/SEM experience required.'
                    ],
                    [
                        'title' => 'Data Analyst',
                        'company' => 'InfoTech Systems',
                        'location' => 'Chicago, IL',
                        'type' => 'Full-time',
                        'salary' => '$65,000 - $85,000',
                        'experience' => '2+ years',
                        'logo' => 'https://placehold.co/100x100',
                        'posted' => '3d ago',
                        'description' => 'Analyze business metrics and create data visualization reports. SQL/Python proficiency required.'
                    ]
                ];

                foreach ($jobs as $job) {
                    echo '
                    <div class="job-card">
                        <div class="card-body">
                            <div class="d-flex">
                                <img src="' . $job['logo'] . '" class="company-logo" alt="' . $job['company'] . ' logo">
                                <div>
                                    <h5 class="job-title">' . $job['title'] . '</h5>
                                    <p class="job-meta">' . $job['company'] . ' • ' . $job['location'] . '</p>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap mb-3">
                                <span class="badge bg-primary">' . $job['type'] . '</span>
                                <span class="badge bg-secondary">' . $job['experience'] . ' Experience</span>
                                ' . (isset($job['urgent']) ? '<span class="badge urgent-badge">Urgent</span>' : '') . '
                                ' . (isset($job['featured']) ? '<span class="badge featured-badge">Featured</span>' : '') . '
                            </div>
                            <p class="salary-range">' . $job['salary'] . '</p>
                            <p class="job-description">' . $job['description'] . '</p>
                            <div class="job-actions">
                                <small>Posted ' . $job['posted'] . '</small>
                                <div>
                                    <a href="#" class="outline">Save Job</a>
                                    <a href="#">Apply Now</a>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
                ?>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>